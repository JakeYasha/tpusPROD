<?php

namespace App\Model\Component;

trait ExtendedNameTrait {

	/**
	 * @return \App\Model\Component\ExtendedName
	 */
	public function extendedNameComponent() {
		return $this->component('ExtendedName');
	}

}
