<?php

namespace App\Classes;

use App\Classes\Controller;
use Sky4\App;
use Sky4\Component\Email;
use Sky4\Exception;
use Sky4\Helper\DeprecatedDateTime as CDateTime;
use Sky4\Helper\CustomObject;
use Sky4\Model;
use const APP_DIR_PATH;
use function app;
use function str;

class EmailSender extends Controller {

	private $body = null,
			$email = null,
			$from = null,
			$subject = null,
			$template = null,
			$template_sub_dir = null,
			$to = [];

	/**
	 * 
	 * @return Email
	 */
	public function email() {
		if ($this->email === null) {
			$this->email = new Email();
			$this->email->setDriverName('Smtp')
					->setFrom(app()->config()->get('app.email.sender.email'), 'Товары плюс');
			$this->email->driver()
					->setAuthenticate(true)
					->setHostName('smtp.yandex.ru')
					->setHostPort(465)
					->setSecureProtocol('ssl')
					->setUserName(app()->config()->get('app.email.sender.email'))
					->setUserPassword(app()->config()->get('app.email.sender.password'));
		}
		return $this->email;
	}

	public function setBody($text) {
		$this->body = $text;
		return $this;
	}

	public function setFrom($from) {
		$this->from = (string) $from;
		return $this;
	}

	public function setSubject($subject) {
		$this->subject = (string) $subject;
		return $this;
	}

	public function setTo($to) {
		if (is_array($to)) {
			foreach ($to as $address) {
				$this->to[] = trim($address);
			}
        } else if(strpos($to, ',') !== FALSE) {
            $_to = explode(',',$to);
            return $this->setTo($_to);
		} else {
			$this->to[] = (string) $to;
		}

		$this->to = array_filter($this->to);

		return $this;
	}

	public function setTemplate($template, $sub_dir = null) {
		$this->template = (string) $template;
		if ($sub_dir === null && $this->model() instanceof Model) {
			$this->template_sub_dir = str()->toLower(Object::className($this->model()));
		}
		$this->template_sub_dir = $sub_dir;

		return $this;
	}

	public function send() {
		$this->compose()
				->email()
				->setFrom($this->from)
				->setSubject($this->subject)
				->setHtmlText($this->body);

		foreach ($this->to as $address) {
			$this->email()->addTo($address);
		}

		$this->email()->send();
		$this->reset();

		return $this;
	}

	public function sendSmsToQuery($cell_phone_number) {
		$this->compose();

		$data = [
			'from' => $this->from,
			'to' => $this->to,
			'subject' => $this->subject,
			'body' => $this->body
		];

		app()->db()->query()
				->setText('INSERT INTO `app_email_queue` SET `data` = :data, `timestamp_inserting` < :timestamp, `type` = :type, `cell_phone_number` = :cell_phone_number')
				->execute([
					':data' => base64_encode(serialize($data)),
					':timestamp' => date(CDateTime::FORMAT),
					':type' => 'sms',
					':cell_phone_number' => trim($cell_phone_number)
		]);

		$this->reset();

		return $this;
	}

	public function sendToQuery() {
		$this->compose();

		$data = [
			'from' => $this->from,
			'to' => $this->to,
			'subject' => $this->subject,
			'body' => $this->body
		];

		app()->db()->query()
				->setText('INSERT INTO `app_email_queue` SET `data` = :data, `timestamp_inserting` < :timestamp, `type` = :type')
				->execute([
					':data' => base64_encode(serialize($data)),
					':timestamp' => date(CDateTime::FORMAT),
					':type' => 'email'
		]);

		$this->reset();

		return $this;
	}

	public function reset() {
		$this->body = null;
		$this->email = null;
		$this->from = null;
		$this->subject = null;
		$this->template = null;
		$this->template_sub_dir = null;
		$this->to = [];

		return $this;
	}

	/**
	 * 
	 * @param [] $array
	 * @return AEmailSender
	 */
	public function setVals($array) {
		$this->from = $array['from'];
		$this->to = $array['to'];
		$this->subject = $array['subject'];
		$this->body = $array['body'];

		return $this;
	}

	private function compose() {
		if ($this->from === null) {
			$this->from = app()->config()->getFrontConfig('app.email.sender.email');
		}

		if ($this->body === null && $this->template !== null) {
			$this->body = $this->view()
					->setBasicSubdirName($this->template_sub_dir)
					->set('item', $this->model === null ? [] : $this->model()->getVals())
					->set('params', $this->params)
					->setDirPath(APP_DIR_PATH . '/src/views')
					->setTemplate($this->template, $this->template_sub_dir)
					->render();
		}

		if ($this->body !== null && $this->from !== null && $this->subject !== null) {
			return $this;
		} else {
			throw new Exception();
		}
	}

}
