<?php

/**
 * @created Nov 29, 2017
 * @author Dmitriy Mitrofanov <d.i.mitrofanov@gmail.com>
 */
namespace App\Action\Crontab;

use App\Model\Brand;
use App\Model\BrandPrice;
use Sky4\Exception;
use function app;
use function str;

class BrandMaker extends \App\Action\Crontab {

	public function execute() {
		$this->startAction()->log('Заполнение таблицы brand и brand_price');
		$total_inserted_rows = 0;
		$time = time();
		$limit = 10000;

		$this->createTempTables();

		$i = -1;
		while (1) {
			$brands = [];
			$i++;
			$data = app()->db()->query()
					->setText('SELECT `id`, `name`, `vendor` FROM `price` WHERE ((name REGEXP " [a-zA-Z\-]{2,} [/(][а-яА-Я \-]{2,}[/)]" OR name REGEXP "[а-яА-Я \-]{2,} [/(][a-zA-Z \-]{2,}[/)]") AND `source` = "ratiss") OR `vendor` != "" LIMIT ' . (($i * $limit)) . ', ' . $limit)
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

				if ($row['vendor']) {
					if (!isset($brands[$row['vendor']])) {
						$brands[$row['vendor']] = [];
					}
					$brands[$row['vendor']][] = $row['id'];
				} else {
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
						}
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

			if ($i % 100000 === 0) {
				$this->log('Обработано ' . $i, 1);
			}
		}
		$this->log('Обработано ' . $i, 1);
		$this->cleaning();
		$this->flipTables();

		$this->endAction();
	}

	public function cleaning() {
		app()->db()->query()
				->setText('DELETE FROM `tmp_brand` WHERE `count` = :0')
				->execute([':0' => 0]);
	}

	public function flipTables() {
		app()->db()->query()->renameTable('brand', 'del_brand');
		app()->db()->query()->renameTable('tmp_brand', 'brand');
		app()->db()->query()->dropTable('del_brand');

		app()->db()->query()->renameTable('brand_price', 'del_brand_price');
		app()->db()->query()->renameTable('tmp_brand_price', 'brand_price');
		app()->db()->query()->dropTable('del_brand_price');
	}

	public function createTempTables() {
		try {
			app()->db()->query()->dropTable('tmp_brand');
		} catch (Exception $exc) {
			;
		}
		try {
			app()->db()->query()->dropTable('tmp_brand_price');
		} catch (Exception $exc) {
			;
		}

		app()->db()->query()->copyTable('brand', 'tmp_brand');

		app()->db()->query()->copyTable('brand_price', 'tmp_brand_price');
		app()->db()->query()
				->setText('UPDATE `tmp_brand` SET `count` = :0')
				->execute([':0' => 0]);
	}

}
