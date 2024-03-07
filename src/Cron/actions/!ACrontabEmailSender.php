<?php

class ACrontabEmailSender extends ACrontabAction {

	public function run() {
		$all = $this->db
				->query()
				->setText("SELECT * FROM `app_email_queue` WHERE `timestamp_inserting` < :time")
				->setParam(':time', \Sky4\Helper\DeprecatedDateTime::now())
				->fetch();

		if ($all) {
			foreach ($all as $ob) {
				$data = unserialize(base64_decode($ob['data']));
                                //$this->log('### Подготовка отправки письма "'.$data['subject'].'"');
				$email = App::email()->email();
				$email->clearRecipients();
                                $email->clearReplyTo();

				if ((string) $ob['type'] === 'email') {
					$email->setFrom($data['from'], 'Товары плюс');
					
                                        foreach ($data['to'] as $address) {
						$email->addTo($address, '');
					}

                                        /*foreach ($data['replyto'] as $address) {
						$email->addReplyTo($address, '');
					}*/
                                        
                                        try {
                                                $email->setSubject($data['subject'])
							->setHtmlText($data['body'])
							->send();
                                        } catch (\Sky4\Exception $ex) {
                                                $this->log($ex->getMessage());
                                        }

					$log_text = 'отправлено письмо с темой ' . $data['subject'] . ' на адрес(а):' . PHP_EOL;
					foreach ($data['to'] as $address) {
						$log_text .= ' ->' . $address;
					}

					$this->log($log_text);
				} elseif ((string) $ob['type'] === 'sms' && $ob['cell_phone_number']) {
					$email
							->setFrom($data['from'], 'Товары плюс')
							->addTo('podati.sms@gmail.com')
							->setSubject(preg_replace('~[^0-9]~u', '', $ob['cell_phone_number']))
							->setHtmlText($data['body'])
							->send();

					$log_text = 'отправлено смс на номер: ' . preg_replace('~[^0-9]~u', '', $ob['cell_phone_number']);
					$this->log($log_text);
				}

				$this->db
						->query()
						->setText("DELETE FROM `app_email_queue` WHERE `id` = :id")
						->execute([':id' => $ob['id']]);
			}
		}
	}

}
