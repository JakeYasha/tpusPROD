<?php

namespace App\Classes\Traits;

trait EmailNoReply {

	protected $email_no_reply = null;

	/**
	 * @return \CEmail
	 */
	public function emailNoReply() {
		if ($this->email_no_reply === null) {
			$this->email_no_reply = new \CEmail();
			$this->email_no_reply->setDriverName('Smtp')
					->setFrom('')
					->addReplyTo('');
			$this->email_no_reply->driver()
					->setAuthenticate(true)
					->setHostName('smtp.yandex.ru')
					->setHostPort(465)
					->setSecureProtocol('ssl')
					->setUserName('')
					->setUserPassword('');
		}
		return $this->email_no_reply;
	}

}
