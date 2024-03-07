<?php

namespace App\Action\PhotoContest;

class AddPhoto extends \App\Action\PhotoContest {

	public function execute($id = 0) {
		$this->findModelObject($id);
		$this->params = app()->request()->processGetParams([
			'nomination' => ['type' => 'int'],
			'error' => ['type' => 'string']
		]);

		app()->breadCrumbs()
				->setElem('Фото-конкурсы', '/photo-contest/')
				->setElem($this->model()->name(), $this->model()->link())
				->setElem('Добавление фотографии');

		app()->metadata()->setFromModel($this->model());
		app()->metadata()->setTitle(app()->metadata()->getTitle() . ' - добавление фотографии');

		$form = new \App\Model\PhotoContestItem\FormAdd();

		$nominations = $this->model()->getNominations();
		$item = $this->model()->prepare();

		$errors = [
			1 => 'Вы уже загрузили фотографию в эту номинацию',
			2 => 'Вы не загрузили фотографию',
			3 => 'Данная номинация или конкурс не активны'
		];

		return $this->view()
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('errors', $errors)
						->set('filters', $this->params)
						->set('item', $item)
						->set('form', $form->render($nominations, $item, $this->params))
						->setTemplate('add-photo')
						->save();
	}

}
