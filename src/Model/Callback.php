<?php

namespace App\Model;

class Callback extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function cols() {
		return [
			'user_name' => [
				'label' => 'Имя пользователя'
			],
			'user_phone' => [
				'label' => 'Телефон'
			]
		];
	}

	public function fields() {
		return array(
			'flag_is_new' => array(
				'elem' => 'single_check_box',
				'label' => 'Новое?'
			),
			'subject' => array(
				'elem' => 'text_field',
				'label' => 'Тема',
				'params' => array(
					'rules' => array('length' => array('max' => 255))
				)
			)
		);
	}

	public function title() {
		return 'Заказ звонка';
	}

}
