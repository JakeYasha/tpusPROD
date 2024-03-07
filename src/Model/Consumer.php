<?php

namespace App\Model;
class Consumer extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait,
	 Component\MetadataTrait;

	public function defaultOrder() {
		return ['timestamp_inserting' => 'desc'];
	}

	public function cols() {
		$cols = [
			'user_name' => [
				'label' => 'Имя пользователя'
			],
			'user_email' => [
				'label' => 'Email'
			],
			'specialist_name' => [
				'label' => 'Специалист'
			],
			'timestamp_inserting' => [
				'label' => 'Дата вопроса',
				'style_class' => 'date-time'
			]
		];

		return array_merge($cols, $this->activeComponent()->cols());
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'UserData'],
			['type' => 'label', 'text' => 'Вопрос'],
			['type' => 'field', 'name' => 'question'],
			['type' => 'component', 'name' => 'TimestampAction'],
			//
			['type' => 'tab', 'name' => 'answer_tab', 'label' => 'Ответ'],
			['type' => 'field', 'name' => 'specialist_name', 'tab_name' => 'answer_tab'],
			['type' => 'field', 'name' => 'answer', 'tab_name' => 'answer_tab'],
			['type' => 'field', 'name' => 'answer_timestamp', 'tab_name' => 'answer_tab'],
			['type' => 'field', 'name' => 'flag_is_sended', 'tab_name' => 'answer_tab'],
			//
			['type' => 'tab', 'name' => 'meta_tab', 'label' => 'Метаданные'],
			['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'meta_tab'],
		];
	}

	public function fields() {
		// type
		return array(
			'answer' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_2'
				],
				'elem' => 'tiny_mce',
				'label' => 'Ответ',
				'params' => [
					'parser' => true
				]
			],
			'question' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_2'
				],
				'elem' => 'tiny_mce',
				'label' => 'Вопрос',
				'params' => [
					'parser' => true
				]
			],
			'specialist_name' => [
				'elem' => 'text_field',
				'label' => 'Специалист',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			],
			'flag_is_sended' => [
				'elem' => 'single_check_box',
				'label' => 'Отправить ответ?',
				'default_val' => 0
			],
			'answer_timestamp' => [
				'elem' => 'date_time_field',
				'label' => 'Время ответа'
			]
		);
	}

	public function title() {
		return $this->exists() ? $this->val('user_name') : 'Потребительские вопросы';
	}

	public function linkItem() {
		return '/consumer/show/' . $this->id() . '/';
	}
	
	public function beforeUpdate(&$vals) {
		if (isset($vals['flag_is_sended']) && $vals['flag_is_sended']) {
			$tc = new Consumer();
			$tc->setVals($vals);
			$tc->setVal('id',$this->id());
			app()->email()
					->setSubject('Ответ на вопрос по защите прав потребителей с сайта tovaryplus.ru')
					->setTo($vals['user_email'])
					->setModel($tc)
					->setTemplate('email_to_user', 'consumer')
					->sendToQuery();
			
			$vals['flag_is_sended'] = 0;
			$vals['answer_timestamp'] = date("Y-m-d H:i:s");
		}

		return parent::beforeUpdate($vals);
	}

	public function afterUpdate(&$vals) {
		return parent::afterUpdate($vals);
	}

}
