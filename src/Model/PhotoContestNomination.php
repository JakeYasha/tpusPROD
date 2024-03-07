<?php

namespace App\Model;
class PhotoContestNomination extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\ImageTrait,
	 Component\TimestampActionTrait;
	
	public function cols() {
		return $this->nameComponent()->cols() + $this->timestampActionComponent()->cols('timestamp_inserting');
	}
	
	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function imageResolutions() {
		return [
			'image' => [
				['width' => 270, 'height' => 170]
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Номинация';
	}

}
