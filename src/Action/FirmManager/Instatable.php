<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Rubric;
use Sky4\Model\Utils;
use function app;

class Instatable extends FirmManager {

	public function execute($id_material = 0) {
            app()->metadata()->setJsFile('/js/jquery.simulate.js')
                    ->setJsFile('/js/jquery.minicolors.js')
                    ->setJsFile('/js/sky/plugins/tinymce-5.1.0/tinymce.min.js')
                    ->setJsFile('/js/styler.material.constructor.js?v=' . time())
                    ->setJsFile('/js/material.constructor.js?v=' . time());
            app()->metadata()->setCssFile('/css/jquery.minicolors.css')
                    ->setCssFile('/css/material.constructor.css?v=' . time());
            
            
            $this->view()
                ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                ->setTemplate('instatable', 'firmmanager')
                ->save();

		return $this;
	}

}
