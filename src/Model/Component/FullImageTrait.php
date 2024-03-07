<?php

namespace App\Model\Component;

trait FullImageTrait {

	/**
	 * 
	 * @return FullImage
	 */
	public function fullImageComponent() {
		return $this->component('FullImage');
	}

}
