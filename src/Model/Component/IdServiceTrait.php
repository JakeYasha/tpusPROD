<?php

namespace App\Model\Component;

trait IdServiceTrait {

	/**
	 *
	 * @return IdService
	 */
	public function idServiceComponent() {
		return $this->component('IdService');
	}

	public function id_service() {
		return (int) $this->val('id_service');
	}

}
