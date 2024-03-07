<?php

namespace App\Model;

class YmlParam extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\NameTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		$c = $this->fieldPropCreator();

		return [
			'id_yml_offer' => $c->intField('ID YML-предложения', 8),
			'val' => $c->stringField('Значение', 500)
		];
	}

}
