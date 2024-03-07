<?php

namespace App\Model;

class GuestBook extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\AnswerTrait,
	 Component\NewStateTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function cols() {
		$cols = [
			'user_name' => [
				'label' => 'Имя пользователя'
			],
			'user_email' => [
				'label' => 'Email'
			]
		];

		return array_merge($cols, $this->activeComponent()->cols(), $this->timestampActionComponent()->cols('timestamp_inserting'));
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'UserData'],
			['type' => 'label', 'text' => 'Сообщение'],
			['type' => 'field', 'name' => 'subject'],
			['type' => 'field', 'name' => 'text'],
			['type' => 'component', 'name' => 'TimestampAction'],
			['type' => 'component', 'name' => 'Active'],
			['type' => 'component', 'name' => 'Answer'],
		];
	}

	public function fields() {
		return [
			'flag_is_new' => [
				'elem' => 'single_check_box',
				'label' => 'Новое?'
			],
			'subject' => [
				'elem' => 'text_field',
				'label' => 'Тема',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			]
		];
	}

	public function afterUpdate(&$vals) {
		if (isset($vals['answer_can_send']) && $vals['answer_text']) {
			app()->email()
					->setSubject('Ответ на ваш отзыв на сайте Tovaryplus.ru')
					->setTo($vals['user_email'])
					->setModel($this)
					->setTemplate('email_to_user', 'guestbook')
					->sendToQuery();
		}
		return parent::afterUpdate($vals);
	}

	public function title() {
		return 'Отзывы';
	}

}
