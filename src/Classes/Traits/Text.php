<?php

namespace App\Classes\Traits;

trait Text {

	protected $text = null;

	/**
	 * @return \App\Model\Text
	 */
	public function text() {
		if ($this->text === null) {
			$this->text = new \App\Model\Text();
		}
		return $this->text;
	}

}
