<?php

namespace App\Classes;

class Action extends \CAction {

	use \App\Classes\Traits\ControllerExtension,
	 \App\Classes\Traits\Text;
        public function view() {
		if ($this->view === null) {
			$this->view = new $this->view_name();
			$this->view->setBasicSubdirName($this->getTemplateSubdirName())
					->setTemplate($this->getTemplate());
		}

		return $this->view;
	}
}
