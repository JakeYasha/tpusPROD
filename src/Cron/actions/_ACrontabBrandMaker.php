<?php

class ACrontabBrandMaker extends ACrontabAction {

	public function run() {
		$this->log('заполнение таблицы brand и brand_price');
		$total_inserted_rows = 0;
		$time = time();
		$limit = 10000;

		$this->createTempTables();

		$i = -1;
		while (1) {
			$brands = [];
			$i++;
			$data = $this->db->query()
					->setText('SELECT `id`, `name` FROM `sts_price` WHERE (name REGEXP " [a-zA-Z\-]{2,} [/(][а-яА-Я \-]{2,}[/)]" OR name REGEXP  "[а-яА-Я \-]{2,} [/(][a-zA-Z \-]{2,}[/)]") LIMIT ' . (($i * $limit)) . ', ' . $limit)
					->fetch();

			if (!$data) {
				break;
			}

			$arr = [];
			foreach ($data as $row) {
				$name = $row['name'];
				preg_match("~ (([a-zA-Z-]{2,}) ([a-zA-Z-0-9+]{2,} [a-zA-Z-0-9+]{2,})|([a-zA-Z-]{2,}) ([a-zA-Z-]{2,})|([a-zA-Z-]{2,})) [/(]([ а-яА-Я-]{2,})[/)]~u", $name, $matches);

				if (!isset($matches[1])) {
					preg_match("~ ([ а-яА-Я-]{2,}) ?[/(]([a-zA-Z-]{2,})[/)]~u", $name, $matches);
					if (isset($matches[2])) {
						$matches[1] = $matches[2];
					}
				}

				if (isset($matches[1]) && (isset($matches[2]) || isset($matches[4]) || isset($matches[6]))) {
					$mark = trim($matches[1]);
					if (isset($matches[2]) and $matches[2]) {
						$mark = trim($matches[2]);
					}
					if (isset($matches[4]) and $matches[4]) {
						$mark = trim($matches[4]);
					}
					if (isset($matches[6]) and $matches[6]) {
						$mark = trim($matches[6]);
					}

					$m1 = trim(preg_replace("~^[a-zA-Z-_0-9]{1,2} ~u", "", $mark));
					if ($m1) {
						if (!isset($brands[$m1])) {
							$brands[$m1] = [];
						}
						$brands[$m1][] = $row['id'];
						//$arr[$row['id']] = array('enname' => $m1, 'id_subgroup' => $row['id_subgroup']);
					}
				}
			}

			foreach ($brands as $brand_name => $price_ids) {
				$hash = md5($brand_name);
				$brand = new Brand();
				$brand->setTable('tmp_brand');
				$brand->getByConds(null, '`hash` = :hash', null, [':hash' => $hash]);

				if (!$brand->exists()) {
					//$brand->insert(['hash' => $hash, 'name' => $brand_name, 'site_name' => str()->firstCharToUpper(str()->toLower($brand_name)), 'count' => count($price_ids)]);
					$brand->insert(['hash' => $hash, 'name' => $brand_name, 'site_name' => str()->firstCharToUpper(str()->toLower($brand_name)), 'count' => count($price_ids)]);
				} else {
					$brand->update(['count' => $brand->val('count') + count($price_ids)]);
				}

				foreach ($price_ids as $id) {
					$bp = new BrandPrice();
					$bp->setTable('tmp_brand_price');
					$bp->insert(['brand_id' => $brand->id(), 'price_id' => (int) $id]);
				}
			}

			//print_r("\r" . $i . 'RAM: ' . round(memory_get_usage() / 1024, 1) . 'Kb');
		}

		$this->cleaning();
		$this->flipTables();

		//echo "\n\n" . time() - $time;

		$this->log('завершено');
	}

	public function cleaning() {
		App::db()->query()
				->setText('DELETE FROM `tmp_brand` WHERE `count` = :0')
				->execute([':0' => 0]);
	}

	public function flipTables() {
		App::db()->query()->renameTable('brand', 'del_brand');
		App::db()->query()->renameTable('tmp_brand', 'brand');
		App::db()->query()->dropTable('del_brand');

		App::db()->query()->renameTable('brand_price', 'del_brand_price');
		App::db()->query()->renameTable('tmp_brand_price', 'brand_price');
		App::db()->query()->dropTable('del_brand_price');
	}

	public function createTempTables() {
		try {
			App::db()->query()->dropTable('tmp_brand');
		} catch (\Sky4\Exception $exc) {
			;
		}
		try {
			App::db()->query()->dropTable('tmp_brand_price');
		} catch (\Sky4\Exception $exc) {
			;
		}

		App::db()->query()->copyTable('brand', 'tmp_brand');

		App::db()->query()->copyTable('brand_price', 'tmp_brand_price');
		App::db()->query()
				->setText('UPDATE `tmp_brand` SET `count` = :0')
				->execute([':0' => 0]);
	}

}
