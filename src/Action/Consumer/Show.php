<?php

namespace App\Action\Consumer;

use App\Model\Consumer\FormAdd;

class Show extends \App\Action\Consumer {

	public function execute($id = '') {
		$this->findModelObject($id);
		if (str()->sub(app()->request()->getRequestUri(), -1) !== '/') {
			app()->response()->redirect(app()->request()->getRequestUri() . '/', 301);
		}
		app()->breadCrumbs()
				->setElem('Защита прав потребителей', '/consumer/')
				->setElem($this->model()->val('metadata_title'));
		/* app()->metadata()->setFromModel($this->model()); */
		/* app()->metadata()->setFromModel($this->model(), $this->model()->val('metadata_title'). ' - title', $this->model()->val('metadata_keywords'). '- keywords', $this->model()->val('metadata_description') . ' - description'); */
		app()->metadata()
				->setTitle($this->model()->val('metadata_title') . ' - вопросы и ответы')
				->setMetatag('description', $this->model()->val('metadata_description') . ' - комментарий специалиста по защите прав потребилей')
				->setMetatag('keywords', $this->model()->val('metadata_key_words') . ', вопросы и ответы по защите прав потребилей');


		$form = new FormAdd();

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('form', $form->render())
				->set('item', $this->model())
				->setTemplate('item')
				->save();
	}

}
