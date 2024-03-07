<?php

namespace App\Action\FirmUser\Price;

class Submit extends \App\Action\FirmUser\Price {

	public function execute() {
		$form = new \App\Model\Price\FormAdd();
		$form->setInputVals($_POST);
		if (!$form->errorHandler()->hasErrors()) {
			$date_time = new \Sky4\Helper\DateTime();
			$vals = $form->getVals();
			$price_catalog = new \App\Model\PriceCatalog($vals['id_catalog']);
			$price = new \App\Model\Price();

			if (isset($vals['id']) && (int)$vals['id'] !== 0) {
				$price->reader()->object($vals['id']);
			}
			
			if(isset($vals['name'])) {
				$vals['name'] = str()->firstCharToUpper($vals['name']);
			}
			
			$vals['description'] = strip_tags($vals['description']);

			if (!$price->exists()) {
				$result_vals = [
					'legacy_id_price' => 0,
					'legacy_id_service' => $this->firm()->id_service(),
					'legacy_id_city' => $this->firm()->val('id_city'),
					'legacy_id_firm' => $this->firm()->val('id_firm'),
					'id_firm' => $this->firm()->id(),
					'id_subgroup' => $price_catalog->id_subgroup(),
					'id_group' => $price_catalog->id_group(),
					'country_of_origin' => $vals['country_of_origin'],
					'currency' => 'RUR',
					'name' => $vals['name'],
					'unit' => $vals['unit'],
					'description' => $vals['description'],
					'vendor' => $vals['vendor'],
					'source' => 'client',
					'price' => $vals['price'],
					'price_old' => $vals['price_old'],
					'price_wholesale' => $vals['price_wholesale'],
					'price_wholesale_old' => $vals['price_wholesale_old'],
					'flag_is_active' => 1,
					'flag_is_available' => (int)$vals['flag_is_available'],
					'flag_is_delivery' => (int)$vals['flag_is_delivery'],
					'flag_is_retail' => (int)$vals['price'] !== 0,
					'flag_is_wholesale' => (int)$vals['price_wholesale'] !== 0,
					'flag_is_image_exists' => 0,
				];
				$price->insert($result_vals);
			} else {
				$result_vals = [
					'country_of_origin' => $vals['country_of_origin'],
					'currency' => 'RUR',
					'name' => $vals['name'],
					'unit' => $vals['unit'],
					'description' => $vals['description'],
					'vendor' => $vals['vendor'],
					'source' => 'client',
					'price' => $vals['price'],
					'price_old' => $vals['price_old'],
					'price_wholesale' => $vals['price_wholesale'],
					'price_wholesale_old' => $vals['price_wholesale_old'],
					'flag_is_active' => 1,
					'flag_is_available' => (int)$vals['flag_is_available'],
					'flag_is_delivery' => (int)$vals['flag_is_delivery'],
					'flag_is_retail' => (int)$vals['price'] !== 0,
					'flag_is_wholesale' => (int)$vals['price_wholesale'] !== 0
				];
				$price->update($result_vals);
			}
			
			\App\Model\PriceCatalogPrice::replace($price, $price_catalog);
			$price->update(['legacy_id_price' => $price->id(), 'id_external' => $price->id()]);
			$this->updateImages($price);

			app()->response()->redirect('/firm-user/price/?success');
		}
	}

	protected function updateImages(\App\Model\Price &$price) {
		$ff = new \App\Model\Image();
		$images = $ff->reader()
				->setWhere(['AND', '`id_firm` = :id_firm', 'source = :source'], [':id_firm' => $this->firm()->id(), ':source' => 'temp'])
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		if ($images) {
			$image_vals = [
				'flag_is_image_exists' => 1
			];

			foreach ($images as $image) {
				$image->update(['id_price' => $price->id(), 'source' => 'client']);
			}

			$price->update($image_vals);
		}

		return $this;
	}

}
