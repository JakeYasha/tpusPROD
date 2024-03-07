<?php

namespace App\Model\Component;

trait IdPriceTrait {

	/**
	 *
	 * @return IdPrice
	 */
	public function idPriceComponent() {
		return $this->component('IdPrice');
	}

	public function id_price() {
		return (int) $this->val('id_price');
	}

}
