<?php

class ACrontabSitemap extends ACrontabAction {

	private $file_descriptor;
	private $file_path;

	public function __construct() {
		$this->file_path = APP_DIR_PATH . '/app/cron/log/sitemap.txt';
		$this->file_descriptor = fopen($this->file_path, 'w+');
		return parent::__construct();
	}

	public function run() {
		$this->log('построение sitemap');
		$this->parseCities();
		$this->parseFirms();
		$this->parseFirmTypes();
		$this->parseSubgroups();
		$this->parseCatalogs();
                $this->parsePages();
		$this->parseMaterials();
		$this->makeSitemap();
		fclose($this->file_descriptor);
		$this->log('завершено');

		return parent::run();
	}

	private function makeSitemap() {
		$sitemap = new ASitemap_0_9();
		$sitemap->setLoc(APP_URL)
				->setDirPath(APP_DIR_PATH . '/public');

		$sitemap->addUrl('/');
		$file = fopen($this->file_path, 'r');
		while (!feof($file)) {
			$sitemap->addUrl(fgetss($file, 5000));
		}

		$sitemap->save();
		fclose($file);
	}
        
        private function parseCities() {
                $fft = new \App\Model\FirmFirmType();
                $items = $fft->reader()
                        ->setSelect(['`id_city`', 'SUM(`flag_is_active`) as `count`'])
                        ->setOrderBy('id_city DESC')
                        ->setGroupBy('`id_city`')
                        ->rows();
                
                if (!$items) {
                        return;
                }

                $out_items = [];
                foreach ($items as $it) {
                        if ((int)$it['count'] > 0 && $it['id_city'] != '76004') {
                                $out_items[$it['id_city']] = '/' . $it['id_city'] . PHP_EOL;
                        }
                }

                foreach ($out_items as $val) {
                        fwrite($this->file_descriptor, $val);
                }
        }

	private function parseFirms() {
		$f = new Firm();
		$items = $f->setSelect(['id', 'id_firm', 'id_service'])
                        ->setWhere(['AND','flag_is_active = :flag_is_active'],[':flag_is_active' => 1])
                        ->getAll();

		$i = 0;
		foreach ($items as $it) {
			$i++;
			fwrite($this->file_descriptor, $it->link() . PHP_EOL);
		}
	}
        
        private function parseSubgroups() {
                $sc = new SubgroupCount();
                
                $items = $sc->reader()
                                ->setSelect(['id', 'id_group', 'id_subgroup', 'id_city'])
                                ->setOrderBy('id_city DESC, id_group ASC, id_subgroup ASC')
                                ->rows();

                if (!$items) {
                        return;
                }
                $out_items = [];
                foreach ($items as $it) {
                        if (!isset($out_items[$it['id_city'] . $it['id_group']])) {
                                $out_items[$it['id_city'] . $it['id_group']] = '/' . $it['id_city'] . '/catalog/' . $it['id_group'] . '/' . PHP_EOL;
                        }
                        $out_items[$it['id_city'] . $it['id_group'] . $it['id_subgroup']] = '/' . $it['id_city'] . '/catalog/' . $it['id_group'] . '/' . $it['id_subgroup'] . '/' . PHP_EOL;
                        $out_items[$it['id_city'] . $it['id_group'] . $it['id_subgroup'] . '_'] = '/' . $it['id_city'] . '/catalog/' . $it['id_group'] . '/' . $it['id_subgroup'] . '/?mode=price'  . PHP_EOL;
                }

                foreach ($out_items as $val) {
                        fwrite($this->file_descriptor, $val);
                }
        }

	private function parseCatalogs() {
		$pcc = new PriceCatalogCount();
		$limit = 10000;
		$page = 0;

		$i = 0;
		while (1) {
			$i++;

			$items = $pcc->reader()
					->setSelect(['id', 'id_catalog', 'id_city'])
					->setLimits($limit, $limit * $page)
					->setOrderBy('id_city DESC, id_catalog ASC')
					->rows();

			if (!$items) {
				break;
			}

			$catalog_ids = [];
			foreach ($items as $it) {
				$catalog_ids[$it['id_catalog']] = 1;
			}

			$pc = new PriceCatalog();
			$cats = $pc->reader()->objectsByIds(array_keys($catalog_ids));

			$out_items = [];
			foreach ($items as $it) {
                                if (isset($cats[$it['id_catalog']])) {
                                        $out_items[$it['id_city'] . $it['id_catalog']] = '/' . $it['id_city'] . $cats[$it['id_catalog']]->link() . PHP_EOL;
                                        $out_items[$it['id_city'] . $it['id_catalog'] . '_'] = '/' . $it['id_city'] . $cats[$it['id_catalog']]->link() . '?mode=price'  . PHP_EOL;
                                } else {
                                        $this->log('Ошибка sitemap - id_catalog = '.$it['id_catalog']);
                                }
			}

			foreach ($out_items as $val) {
				fwrite($this->file_descriptor, $val);
			}

			$page++;
		}
	}
        
        private function parseFirmTypes() {
                $fft = new FirmFirmType();
		$limit = 10000;
		$page = 0;
                
		$i = 0;
		while (1) {
			$i++;

			$items = $fft->reader()
                                ->setSelect(['id_type', 'id_city'])
                                ->setWhere(['AND','flag_is_active = :flag_is_active'],[':flag_is_active' => 1])
                                ->setLimits($limit, $limit * $page)
                                ->setOrderBy('id_type ASC')
                                ->rows();

			if (!$items) {
				break;
			}

			$firm_type_ids = [];
			foreach ($items as $it) {
				$firm_type_ids[$it['id_type']] = 1;
			}

			$ft = new FirmType();
			$ftypes = $ft->reader()->objectsByIds(array_keys($firm_type_ids));

			$out_items = [];
			foreach ($items as $it) {
                                if (isset($ftypes[$it['id_type']])) {
                                        $out_items[$it['id_city'] . $it['id_type']] = '/' . $it['id_city'] . $ftypes[$it['id_type']]->link() . PHP_EOL;
                                } else {
                                        $this->log('Ошибка sitemap - id_type = '.$it['id_type']);
                                }
			}

			foreach ($out_items as $val) {
				fwrite($this->file_descriptor, $val);
			}

			$page++;
		}
	}
        
        private function parsePages() {
		$p = new Page();
		$items = $p->setSelect(['id', 'name_in_url'])
                        ->setWhere(['AND', '`state` =:state'], [':state' => 'published'])
                        ->getAll();

		$i = 0;
		foreach ($items as $it) {
			$i++;
			fwrite($this->file_descriptor, $it->linkItem() . PHP_EOL);
		}
	}
    
	private function parseMaterials() {
		$items = app()->db()->query()
            ->setText("SELECT `type`, `mnemonic` FROM `material` WHERE `flag_is_published`='1'")
            ->fetch();
            
        $i = 0;
        foreach ($items as $it) {
			$i++;
            fwrite($this->file_descriptor, '/' . $it['type'] . 's/' . $it['mnemonic'] .'/'. PHP_EOL);
        }
	}

}
