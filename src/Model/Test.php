<?php

namespace App\Model;

class Test extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;

	public function fields() {
		$fc = $this->fieldPropCreator();
		return [
			'name_1' => $fc->textField('Название 1'),
			'big_text' => $fc->tinyMce('Это будет тинимце в админке')
		];
	}

	public function formStructure() {
		$fs = $this->formStructureCreator();
		return [
			$fs->field('name', null, 'special_tab'),
			$fs->component('id'),
			$fs->tab('special_tab', 'Имя таба')
		];
	}

}
