<?php

namespace App\Model;

class WordException extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;

	public function title() {
		return $this->exists() ? $this->name() : 'Слова исключения';
	}

}
