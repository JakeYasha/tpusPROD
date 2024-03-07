<?php

namespace App\Action\Crontab;

class StsStatistics extends \App\Action\Crontab {

	public function __construct($internal_call = false) {
		parent::__construct($internal_call);
	}

	public function execute() {
		$this->startAction();
		$this->loadData('sts_hist_answer')
				->loadDataExt('sts_hist_calls','sts_hist_calls')
				->loadData('sts_hist_export_detail')
				->loadData('sts_hist_readdress');
		$this->endAction();
	}

}
