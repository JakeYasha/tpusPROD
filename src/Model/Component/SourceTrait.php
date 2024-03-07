<?php

namespace App\Model\Component;

trait SourceTrait {

	/**
	 * 
	 * @return Source
	 */
	public function sourceComponent() {
		return $this->component('Source');
	}

	public function sourceIsClient() {
		return (string) $this->val('source') === 'client';
	}

	public function sourceIsRatiss() {
		return (string) $this->val('source') === 'ratiss';
	}

	public function sourceIsYml() {
		return (string) $this->val('source') === 'yml';
	}

	public function source() {
		return (string) $this->val('source');
	}

}
