<?php

namespace App\Action;

use App\Model\Consumer as ConsumerModel;
use App\Model\Consumer\FormAdd;
use App\Presenter\ConsumerItems;
use function app;

class Consumer extends \App\Classes\Action {

	public function execute($id = '') {
		$this->text()->getByLink('/consumer/');
		app()->breadCrumbs()->setElem($this->text()->name(), '');

		$presenter = new ConsumerItems();
		$presenter->find();

		app()->metadata()->setFromModel($this->text(), null, $presenter->pagination());
		if ((int) $presenter->pagination()->getPage() != 1) {
			app()->metadata()->setCanonicalUrl('/customer/');
		}

		//app()->useCaptcha();
		$form = new FormAdd();

		$errors = [];
		if ($form->errorHandler()->hasErrorsInSession()) {
			$errors = $form->errorHandler()->getErrorsFromSession();
			//$form->setInputVals($form->errorHandler()->getValsFromSession());
			$form->setOutputVals($form->errorHandler()->getValsFromSession());
			$form->errorHandler()->resetErrors();
			$form->errorHandler()->removeErrorsFromSession();
		} else {
			$params = app()->request()->processGetParams(['send' => 'int']);
			if ($params['send'] !== null) {
				$this->text()->setVal('text', '<p class="form-send-success">Форма отправлена успешно!</p>' . $this->text()->val('text'));
			}
		}

		if ($presenter->pagination()->getPage() !== 1) {
			app()->metadata()->setCanonicalUrl('/consumer/');
		}

		$this->view()
				->set('errors', $errors)
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('item', $this->text())
				->set('items', $presenter->renderItems())
				->set('form', $form->render())
				->set('short_view', $presenter->pagination()->getPage() === 1 ? false : true)
				->set('pagination', $presenter->pagination()->render())
				->setTemplate('index')
				->save();
	}

	public function __construct() {
		parent::__construct();
		$this->setModel(new ConsumerModel());
	}

}
