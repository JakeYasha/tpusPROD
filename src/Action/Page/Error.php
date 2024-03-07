<?php

namespace App\Action\Page;

class Error extends \App\Action\Page {

	public function execute() {
		app()->frontController()->layout()->setTemplate('code404full');
		return $this->view()
						->setTemplate('page404')
						->save();
	}

}
