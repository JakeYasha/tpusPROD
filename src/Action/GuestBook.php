<?php

namespace App\Action;

class GuestBook extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\GuestBook());
	}

	public function execute() {
		$items = $this->model()->reader()
				->setWhere('`flag_is_active` = :yes', [':yes' => 1])
				->setOrderBy('`timestamp_inserting` DESC')
				->objects();

		$this->text()->getByLink('/guest-book/');
		app()->metadata()->setFromModel($this->text());
		app()->breadCrumbs()->setElem($this->text()->name(), '');

		//app()->useCaptcha();
		$form = new \App\Model\GuestBook\FormAdd();

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

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('items', $items)
				->set('errors', $errors)
				->set('form', $form->render())
				->set('item', $this->text())
				->setTemplate('index')
				->save();
	}

}
