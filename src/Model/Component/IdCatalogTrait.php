<?php

namespace App\Model\Component;

trait IdCatalogTrait {

	/**
	 *
	 * @return IdCatalog
	 */
	public function idСфефдщпComponent() {
		return $this->component('IdCatalog');
	}

	public function id_catalog() {
		return (int) $this->val('id_catalog');
	}

}
