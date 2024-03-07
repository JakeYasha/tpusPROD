<?php

namespace App\Action\Page;

class Show extends \App\Action\Page {

	public function execute($id = 0) {
		$this->findModelObject($id);
		$bc = app()->breadCrumbs()
				->setSeparator('<i>/</i>')
				->setElem($this->model()->name())
				->render();

		$childs = $this->model()->nestedSet()->getChildren();
		app()->metadata()->setFromModel($this->model());

		if (in_array($this->model()->val('id'), [115,117])) {
            app()->setUseAgreement(true);
        }

		$this->view()
				->setTemplate('item')
				->set('item', $this->model())
				->set('breadcrumbs', $bc)
				->set('childs', $childs)
				->set('title', $this->model()->val('title'))
				->save();
		return true;
	}

}
