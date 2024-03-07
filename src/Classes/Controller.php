<?php

namespace App\Classes;

use App\Model\AdvText,
    Sky4\Helper\CustomObject,
    Sky4\Helper\StringHelper;

class Controller extends \CController {

	use \App\Classes\Traits\ControllerExtension,
	 \App\Classes\Traits\Text;
        
        /**
	 * @return View
	 */
	public function view() {
		if ($this->view === null) {
			$this->view = new $this->view_name();
			$this->view->setBasicSubdirName(StringHelper::toLower(Object::classShortName($this)));
		}
		return $this->view;
	}
}
