<?php

namespace App\Action\Utils;

class MailTest extends \App\Action\Utils {

	public function __construct() {
		parent::__construct();
		if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
        app()->email()
				->setSubject('Тестовое письмо')
				->setTo('vae@727373.ru,vae@tovaryplus.ru,mng@tovaryplus.ru,mng@727373.ru')
				->setModel(new \App\Model\Banner(1259))
				->setTemplate('email_banner_notice', 'firmmanager')
				->sendToQuery();

		exit();
	}

}
