<?php

namespace App\Model\Component;

trait MetadataPriceModeTrait {

	/**
	 * 
	 * @return App\Model\Component\MetadataPriceMode
	 */
	public function metadataPriceModeComponent() {
		return $this->component('MetadataPriceMode');
	}

}
