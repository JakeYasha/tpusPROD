<?php

namespace App\Model\PriceCatalog;

class CmsForm extends \Sky4\Model\Form {

	public function __construct(\Sky4\Model $model = null, $params = null) {
		parent::__construct($model, $params);
		app()->metadata()->setJsFile('/js/cms-js.js');
	}

	public function controls() {
		$controls = parent::controls();

		if ($this->model()->exists() && $this->model()->val('node_level') > 2) {
			$controls['reload_catalog'] = [
				'attrs' => ['title' => 'Пересчитать каталог', 'class' => 'js-reload-catalog', 'data-catalog-id' => $this->model()->id()],
				'elem' => 'button',
				'label' => 'Пересчитать каталог',
			];

			$controls['go_to_catalog'] = [
				'attrs' => ['title' => 'Открыть на сайте', 'class' => 'js-open-catalog-on-site', 'data-catalog-href' => $this->model()->link()],
				'elem' => 'button',
				'label' => 'Открыть на сайте',
			];
		}

		return $controls;
	}

}
