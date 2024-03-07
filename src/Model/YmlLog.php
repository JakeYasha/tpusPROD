<?php

namespace App\Model;

class YmlLog extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
			'id_yml' => $c->intField('YML_ID'),
			'offers_count' => $c->intField('Количество предложений в файле', 4),
			'offers_count_loaded' => $c->intField('Количество загруженных предложений', 4),
			'offers_count_active' => $c->intField('Количество активных предложений', 4),
			'url' => $c->stringField('URL Yml-файла', 512),
		];
	}

}
