<?php

namespace App\Model;
class AdvertAgeLimit extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;
	
	public function title() {
		return $this->exists() ? $this->name() : 'Возрастные ограничения';
	}
}
