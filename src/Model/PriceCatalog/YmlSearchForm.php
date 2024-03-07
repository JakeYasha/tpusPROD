<?php

namespace App\Model\PriceCatalog;

class YmlSearchForm extends \Sky4\Model\Form {

	public function __construct($model = null, $params = null) {
		parent::__construct($model, $params);
		$this->setModel(new \App\Model\PriceCatalog());
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'submit_button',
				'label' => 'Применить',
				'attrs' => [
					'class' => 'send js-fix-yml-catalog-id btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-manager/yml/apply-id-catalog/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fc = $this->model()->fieldPropCreator();
		$result['id'] = $fc->autocomplete('Название', 'price-catalog', 'web_name');
		$result['id_yml_category'] = $fc->hiddenField('&nbsp;');

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('sub_heading', '')
						->setTemplate('catalog_name_autocomplete_form', 'forms')
						->render();
	}

}
