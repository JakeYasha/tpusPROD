<?php

namespace App\Action\FirmManager\Yml;

class ToggleCategory extends \App\Action\FirmManager\Yml {

	public function execute() {
		$params = app()->request()->processGetParams([
			'id' => ['type' => 'int'],
		]);

		$ycat = new \App\Model\YmlCategory($params['id']);
		if ($ycat->exists()) {

			if ((int)$ycat->val('flag_is_fixed') === 0 && (int)$ycat->val('id_catalog') !== 0) {
				$applier = new ApplyIdCatalog();
				$applier->execute([
					'id' => (int)$ycat->val('id_catalog'),
					'id_yml_category' => $ycat->id()
				]);
			} else {
				$cat = new \App\Classes\Catalog(true);
				$cat->emptyPriceCatalogForFirm($ycat->id_firm(), $ycat->val('id_catalog'));
			}

			$ycat->update([
				'flag_is_fixed' => ((int)$ycat->val('flag_is_fixed') === 0) ? 1 : 0,
			]);
		}
		die();
	}

}
