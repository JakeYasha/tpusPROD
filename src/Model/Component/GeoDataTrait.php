<?php

namespace App\Model\Component;

trait GeoDataTrait {

	/**
	 *
	 * @return GeoData
	 */
	public function geoDataComponent() {
		return $this->component('GeoData');
	}

}
