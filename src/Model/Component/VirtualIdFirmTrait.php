<?php

namespace App\Model\Component;

trait VirtualIdFirmTrait {

	/**
	 *
	 * @return VirtualIdFirm
	 */
	public function VirtualIdFirmComponent() {
		return $this->component('VirtualIdFirm');
	}

	public function id_virtual() {
		return $this->val('virtual_id_firm');
	}

}
