<?php

namespace App\Action\FirmManager\Yml;

class ApplyIdCatalog extends \App\Action\FirmManager\Yml {

	public function execute($params = null) {
		$mode = 'toggler';
		if ($params === null) {
			$mode = 'self';
			$params = app()->request()->processPostParams([
				'id' => ['type' => 'int'],
				'id_yml_category' => ['type' => 'int']
			]);
		}

		if ($params['id'] && $params['id_yml_category']) {
			$cat = new \App\Model\PriceCatalog($params['id']);
			if ($cat->exists()) {
				$ycat = new \App\Model\YmlCategory($params['id_yml_category']);
				if ($ycat->exists()) {
					$ycat->update([
						'flag_is_fixed' => 1,
						'id_catalog' => $cat->id()
					]);
					$fcat = new \App\Classes\Catalog(true);
					$fcat->storeYml($ycat->id_firm(), $cat->id(), null, true);
				}
			}
		}

		if ($mode === 'self') {
			app()->response()->redirect(isset($_SESSION['firmmanager_yml_categories_url']) ? $_SESSION['firmmanager_yml_categories_url'] : '/firm-manager/yml/categories/');
		}

		return $this;
	}

}
