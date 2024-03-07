<?php

namespace App\Presenter;

class Presenter extends \Sky4\Widget\CollectionView {

	public function attrs() {
		$attrs = [
			'class' => 'js-presenter',
			'data-model-alias' => $this->model()->alias()
		];
		if ($this->model()->exists()) {
			$attrs['data-object-id'] = $this->model()->id();
		}
		$attrs['data-modes'] = implode(',', $this->getModes());
		$attrs['data-order'] = [];
		foreach ($this->getOrder() as $field_name => $order_direction) {
			$attrs['data-order'][] = $field_name . '-' . \str()->toLower($order_direction);
		}
		$attrs['data-order'] = implode(',', $attrs['data-order']);
		$attrs['data-page'] = $this->getPage();
		return $attrs;
	}

	/**
	 * @return \App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new \App\Classes\Pagination();
		}
		return $this->pagination;
	}

}
