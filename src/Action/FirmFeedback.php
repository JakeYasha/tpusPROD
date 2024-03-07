<?php

namespace App\Action;

class FirmFeedback extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\FirmFeedback());
	}

	public function execute() {
		throw new \Sky4\Exception();
	}

    public function renderResult($result, $result_type = 'json', $template = 'success') {
        if ($result_type == 'json') {
            die(json_encode($result));
        } else {
            return $this->view()
                            ->set('result', $result)
                            ->set('breadcrumbs', app()->breadCrumbs()->render())
                            ->setTemplate($template, 'firmfeedback')
                            ->save();
        }
    }
}
