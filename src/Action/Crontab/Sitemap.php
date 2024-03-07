<?php

namespace App\Action\Crontab;

use App\Model\Firm;
use App\Model\FirmFirmType;
use App\Model\FirmType;
use App\Model\Page;
use App\Model\Material;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogPrice;
use App\Model\SubgroupCount;

class Sitemap extends \App\Action\Crontab {

	protected $file_descriptor;
	protected $file_path;

	public function __construct() {
		$this->file_path = APP_DIR_PATH . '/src/Cron/log/sitemap.txt';
		$this->file_descriptor = fopen($this->file_path, 'w+');
		return parent::__construct();
	}

	public function execute() {
		$this->startAction();

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

		$this->endAction();
	}

	private function makeSitemap() {
		$sitemap = new \App\Classes\Sitemap();
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
		$fft = new FirmFirmType();
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
			if ((int) $it['count'] > 0 && $it['id_city'] != '76004') {
                $sts_city = new \App\Model\StsCity();
                $sts_city->reader()->object($it['id_city']);
                $country = (int)$sts_city->val('id_country') == 643 ? false : (int)$sts_city->val('id_country');
				$out_items[$it['id_city']] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . PHP_EOL;
			}
		}

		foreach ($out_items as $val) {
			fwrite($this->file_descriptor, $val);
		}
	}

	private function parseFirms() {
		$f = new Firm();
		$items = $f->setSelect(['id', 'id_firm', 'id_service'])
				->setWhere(['AND', 'flag_is_active = :flag_is_active'], [':flag_is_active' => 1])
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
            $sts_city = new \App\Model\StsCity();
            $sts_city->reader()->object($it['id_city']);
            $country = (int)$sts_city->val('id_country') == 643 ? false : (int)$sts_city->val('id_country');
			if (!isset($out_items[$it['id_city'] . $it['id_group']])) {
				$out_items[$it['id_city'] . $it['id_group']] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . '/catalog/' . $it['id_group'] . '/' . PHP_EOL;
			}
			$out_items[$it['id_city'] . $it['id_group'] . $it['id_subgroup']] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . '/catalog/' . $it['id_group'] . '/' . $it['id_subgroup'] . '/' . PHP_EOL;
			$out_items[$it['id_city'] . $it['id_group'] . $it['id_subgroup'] . '_'] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . '/catalog/' . $it['id_group'] . '/' . $it['id_subgroup'] . '/?mode=price' . PHP_EOL;
		}

		foreach ($out_items as $val) {
			fwrite($this->file_descriptor, $val);
		}
	}

	private function parseCatalogs() {
		$pcp = new PriceCatalogPrice();
		$limit = 10000;
		$page = 0;
		
		//$cities =

		$i = 0;
		while (1) {
			$i++;
			$items = $pcp->reader()
					->setSelect(['id', 'id_catalog', 'id_firm'])
                    ->setWhere(['AND', '`node_level` > :node_level'], [':node_level' => 2])
					->setLimit($limit, $limit * $page)
					->setOrderBy('id_catalog ASC')
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
            $cities = [];
			foreach ($items as $it) {
				if (isset($cats[$it['id_catalog']])) {
                    if (!isset($cities[$it['id_firm']])) {
                        $cities[$it['id_firm']] = (new Firm($it['id_firm']))->val('id_city');
                    }
                    $it['id_city'] = $cities[$it['id_firm']];
                    $sts_city = new \App\Model\StsCity();
                    $sts_city->reader()->object($it['id_city']);
                    $country = (int)$sts_city->val('id_country') == 643 ? false : (int)$sts_city->val('id_country');
                    
					$out_items[$it['id_city'] . $it['id_catalog']] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . $cats[$it['id_catalog']]->link() . PHP_EOL;
					$out_items[$it['id_city'] . $it['id_catalog'] . '_'] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . $cats[$it['id_catalog']]->link() . '?mode=price' . PHP_EOL;
				} else {
					$this->log('Ошибка sitemap - id_catalog = ' . $it['id_catalog']);
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
					->setWhere(['AND', 'flag_is_active = :flag_is_active'], [':flag_is_active' => 1])
					->setLimit($limit, $limit * $page)
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
                    $sts_city = new \App\Model\StsCity();
                    $sts_city->reader()->object($it['id_city']);
                    $country = (int)$sts_city->val('id_country') == 643 ? false : (int)$sts_city->val('id_country');
                    
					$out_items[$it['id_city'] . $it['id_type']] = '/' . ($country ? $country . '-' : '') . $it['id_city'] . $ftypes[$it['id_type']]->link() . PHP_EOL;
				} else {
					$this->log('Ошибка sitemap - id_type = ' . $it['id_type']);
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
//		$p = new Material();
//        
//        $items = $p->setSelect(['type', 'mnemonic'])
//				->setWhere(['AND', '`flag_is_published` =:state'], [':state' => '1'])
//				->getAll();
//		$i = 0;
//		foreach ($items as $it) {
//			$i++;
//			fwrite($this->file_descriptor, '/' . $it->val('type') . 's/' . $it->val('mnemonic') .'/'. PHP_EOL);
//		}
        
//        $items = app()->db()->query()
//            ->setText("SELECT `type`, `mnemonic` FROM `material` WHERE `flag_is_published`='1'")
//            ->fetch();
//            
//        $i = 0;
//        foreach ($items as $it) {
//			$i++;
//            fwrite($this->file_descriptor, '/' . $it['type'] . 's/' . $it['mnemonic'] .'/'. PHP_EOL);
//        }
        fwrite($this->file_descriptor, '/materials/');
//        
//        
	}

}
