<?php

namespace App\Model;

class FirmReview extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\ActiveTrait,
	 Component\NewStateTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function beforeInsert(&$vals, $parent_object = null) {
		$vals['flag_is_new'] = '1';
		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		$vals['flag_is_new'] = '0';
		return parent::beforeUpdate($vals);
	}

	public function afterInsert(&$vals, $parent_object = null) {
		if ($this->firm()->exists()) {
			if ($this->firm()->id_service() === 10 && $this->firm()->id_manager() !== null) {
				$firm_manager = new FirmManager();
				$firm_manager->getByFirm($this->firm());

				if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
					app()->email()
							->setSubject('TovaryPlus.ru: добавлен новый отзыв о фирме ' . $this->firm()->getVal('company_name'))
							->setTo($firm_manager->val('email') !== '' ? $firm_manager->val('email') : $firm_manager->val('email_default'))
							->setModel($this)
							->setTemplate('email_to_manager', 'firmreview')
							->sendToQuery();
				}
			} else {
				$service = new StsService();
				$service->reader()
						->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $this->firm()->id_service()])
						->objectByConds();

				$email = $service->val('email');
				app()->email()
						->setSubject('TovaryPlus.ru: добавлен новый отзыв о фирме ' . $this->firm()->getVal('company_name'))
						->setTo($email)
						->setModel($this)
						->setTemplate('email_to_manager', 'firmreview')
						->sendToQuery();
			}
		}
		return parent::afterInsert($vals, $parent_object);
	}

	public function afterUpdate(&$vals) {
		if (isset($vals['reply_text']) && $vals['reply_text'] && isset($vals['flag_is_reply_send']) && (int) $vals['flag_is_reply_send'] === 0) {
			$firm = new Firm();
			$firm->getByIdFirm($this->id_firm());
			$this->get($vals['id']);

			app()->email()
					->setSubject('TovaryPlus.ru: мы получили ответ на ваш отзыв')
					->setTo($this->val('user_email'))
					->setModel($this)
					->setTemplate('email_to_user_after_reply', 'firmreview')
					->sendToQuery();

			$this->update(['flag_is_reply_send' => 1, 'reply_timestamp' => \Sky4\Helper\DeprecatedDateTime::now()]);
		}
		return parent::afterUpdate($vals);
	}

	public function cols() {
		$cols = [
			//'user_name' => ['label' => 'Пользователь'],
			'user_email' => ['label' => 'Email'],
			'id_firm' => ['label' => 'Фирма'],
			'score' => ['label' => 'Оценка'],
			'flag_is_considered' => ['label' => 'Учитывать<br/>в&nbsp;рейтинге', 'type' => 'flag'],
			'flag_is_active' => ['label' => 'Показывать<br/>на сайте', 'type' => 'flag']
		];
		$cols = array_merge($cols, $this->timestampActionComponent()->cols('timestamp_inserting'));
		$cols = array_merge($cols, $this->newComponent()->cols());

		return $cols;
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function defaultOrder() {
		return ['timestamp_inserting' => 'DESC'];
	}

	public function linkItem() {
		return $this->firm()->linkItem() . '?mode=review';
	}

	public function fields() {
		return [
			'flag_is_considered' => [
				'elem' => 'single_check_box',
				'label' => 'Учитывать в рейтинге',
				'default_val' => 1
			],
			'score' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'name' => 'score',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'Оценка'
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'hidden_field',
				'label' => 'ID города'
			],
			'reply_text' => [
				'attrs' => ['rows' => '10'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Официальный ответ',
				'params' => [
					'parser' => true
				]
			],
			'flag_is_reply_send' => [
				'elem' => 'single_check_box',
				'label' => 'Ответ отправлен',
				'default_val' => 0
			],
			'reply_timestamp' => [
				'elem' => 'date_time_field',
				'label' => 'Время добавления ответа'
			],
			'reply_user_name' => [
				'elem' => 'text_field',
				'label' => 'Автор',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			],
		];
	}

	public function formStructure() {
		$firm = new Firm();
		$firm->getByIdFirm($this->id_firm());
		return [
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'label', 'text' => 'Отзыв'],
			['type' => 'field', 'name' => 'score'],
			['type' => 'field', 'name' => 'text'],
			['type' => 'label', 'text' => 'Пользователь'],
			['type' => 'field', 'name' => 'user_name'],
			['type' => 'field', 'name' => 'user_email'],
			['type' => 'label', 'text' => 'Флаги'],
			['type' => 'field', 'name' => 'flag_is_new'],
			['type' => 'field', 'name' => 'flag_is_considered'],
			['type' => 'field', 'name' => 'flag_is_active'],
		];
	}

	public function filterFields() {
		return [
			'id_firm' => [
				'elem' => 'drop_down_list',
				'label' => 'Фирма',
				'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
				'cond' => '=',
				'field_name' => 'id_firm'
			],
			'flag_is_active' => [
				'elem' => 'single_check_box',
				'label' => 'Только активные',
				'cond' => 'flag',
				'field_name' => 'flag_is_active'
			],
			'flag_is_new' => [
				'elem' => 'single_check_box',
				'label' => 'Только новые',
				'cond' => 'flag',
				'field_name' => 'flag_is_new'
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'flag_is_active'],
			['type' => 'field', 'name' => 'flag_is_new']
		];
	}

	public function title() {
		return $this->exists() ? 'Отзыв № ' . $this->id() : 'Отзывы';
	}

	public function defaultInsertingEnabled() {
		return false;
	}

	public static function prepare(FirmReview $item) {
		$firm = new Firm();
		$firm->getByIdFirm($item->id_firm());

		return [
			'id' => $item->id(),
			'name' => $item->val('name'),
			'user_name' => $item->val('user_name'),
			'user_email' => $item->val('user_email'),
			'score' => $item->val('score'),
			'datetime' => date('d.m.Y H:i', \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'))),
			'text' => $item->val('text'),
			'reply_text' => $item->val('reply_text'),
			'link' => app()->linkFilter($firm->link(), ['id' => $item->id(), 'mode' => 'review']),
			'firm' => $firm,
			'is_active' => (int) $item->val('flag_is_active') === 1
		];
	}

	public static function getChangeScoreHash($id, $email, $score) {
		return md5($id . $email . $score);
	}

}
