<?php

namespace App\Model;

class FirmUserTimestamp extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait;

	public function fields() {
		return [
			'id_firm_user' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID пользователя',
				'params' => [
					'rules' => ['int']
				]
			],
			'last_act_feedback_timestamp' => [
				'elem' => 'date_time_field',
				'label' => 'Последняя активность - сообщения'
			],
			'last_act_request_timestamp' => [
				'elem' => 'date_time_field',
				'label' => 'Последняя активность - заказы'
			],
			'last_act_review_timestamp' => [
				'elem' => 'date_time_field',
				'label' => 'Последняя активность - отзывы'
			]
		];
	}

	public function getByUser($user) {
		if ($user instanceof FirmUser) {
			if ($user->exists()) {
				$this->reader()
						->setWhere([
							'AND',
							'id_firm_user = :id_firm_user',
							'id_firm = :id_firm',
								], [
							':id_firm_user' => $user->id(),
							':id_firm' => $user->id_firm()
						])
						->objectByConds();

				if (!$this->exists()) {
					$this->insert([
						'id_firm_user' => $user->id(),
						'id_firm' => $user->id_firm()
					]);

					$this->refresh();
				}
			}
		}

		return $this;
	}

	public function getTimestampByModel(\Sky4\Model $model) {
		$result = \Sky4\Helper\DeprecatedDateTime::nil();
		$alias = $model->alias();

		switch ($alias) {
			case 'firm-review' :
				$result = $this->val('last_act_review_timestamp');
				break;
			case 'price-request' :
				$result = $this->val('last_act_request_timestamp');
				break;
			case 'firm-feedback' :
				$result = $this->val('last_act_feedback_timestamp');
				break;
		}

		return $result;
	}

	public function touch($field) {
		if ($this->exists()) {
			$this->update([$field => \Sky4\Helper\DeprecatedDateTime::now()]);
		}
		return $this;
	}

	public function touchFeedback() {
		$this->touch('last_act_feedback_timestamp');
		return $this;
	}

	public function touchRequest() {
		$this->touch('last_act_request_timestamp');
		return $this;
	}

	public function touchReviews() {
		$this->touch('last_act_review_timestamp');
		return $this;
	}

}
