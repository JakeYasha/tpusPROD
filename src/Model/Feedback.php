<?php

namespace App\Model;

class Feedback extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function cols() {
		return [
			'user_name' => [
				'label' => 'Имя пользователя'
			],
			'user_email' => [
				'label' => 'Email'
			]
		];
	}

	public function fields() {
		// type
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

	public function title() {
		return 'Вопросы с сайта';
	}

}
