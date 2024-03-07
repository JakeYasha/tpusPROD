<?php

namespace App\Action\AppAjax;

class GetCatalogName extends \App\Action\AppAjax {

	public function execute() {
		$params = app()->request()->processGetParams([
			'id_yml_category' => ['type' => 'int']
		]);

		$form = new \App\Model\PriceCatalog\YmlSearchForm();
		$form->setVal('id_yml_category', $params['id_yml_category']);

		die($this->view()
						->set('id_yml_category', (int) $params['id_yml_category'])
						->set('form', $form->render())
						->setTemplate('catalog_search_popup', 'ymlcategory')
						->render());
	}

}
