<?php

namespace App\Model;
class PhotoContestItem extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\NewStateTrait,
	 Component\NameTrait,
	 Component\ImageTrait,
	 Component\UserDataTrait,
	 Component\TimestampActionTrait,
	 Component\IpAddrTrait;

	public function cols() {
		$cols = [
			'user_name' => ['label' => 'Автор'],
			'user_phone' => ['label' => 'Телефон'],
			'photo_contest_id' => ['label' => 'Конкурс'],
			'nomination_id' => ['label' => 'Номинация'],
		];

		$cols = $cols + $this->timestampActionComponent()->cols('timestamp_inserting') + $this->newComponent()->cols() + $this->activeComponent()->cols();
		$cols['flag_is_winner'] = ['label' => 'Победитель', 'type' => 'flag'];

		return $cols;
	}

	public function getActiveNominationItemsList($nomination_id, $contest_id) {
		$items = [];

		$_items = $this->reader()
				->setWhere(['AND', 'flag_is_active = :active', 'nomination_id = :nomination', 'photo_contest_id = :contest'], [':active' => 1, ':nomination' => $nomination_id, ':contest' => $contest_id])
				->setOrderBy(['timestamp_inserting DESC'])
				->objects();

		$images = \Sky4\Model\Utils::getObjectsFromObjects($_items, 'image');
		foreach ($_items as $item) {
			$items[] = $item->prepare($images);
		}

		return $items;
	}
	
	public function getWinnersList($contest_id, $nominations) {
		$items = [];

		$_items = $this->reader()
				->setWhere(['AND', 'flag_is_active = :active', 'photo_contest_id = :contest', 'flag_is_winner = :flag_is_winner'], [':active' => 1, ':contest' => $contest_id, ':flag_is_winner' => 1])
				->setOrderBy(['timestamp_last_updating DESC'])
				->objects();

		$images = \Sky4\Model\Utils::getObjectsFromObjects($_items, 'image');
		foreach ($_items as $item) {
			$items[] = $item->prepare($images, $nominations);
		}

		return $items;
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function fields() {
		return [
			'flag_is_winner' => [
				'elem' => 'single_check_box',
				'label' => 'Победитель',
				'default_val' => 0
			],
			'photo_contest_id' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'drop_down_list',
				'label' => 'Фото конкурс',
				'options' => \Sky4\Container::getList('PhotoContest')
			],
			'nomination_id' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'drop_down_list',
				'label' => 'Номинация',
				'options' => \Sky4\Container::getList('PhotoContestNomination')
			],
			'user_agent' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'hidden_field'
			],
			'counter_votes' => [
				'elem' => 'text_field',
				'label' => 'Количество голосов',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'timestamp_last_vote' => [
				'elem' => 'date_time_field',
				'label' => 'Время последнего голоса'
			]
		];
	}

	public function imageResolutions() {
		return [
			'image' => [
				['width' => 270, 'height' => 170]
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Фотография';
	}

	public function prepare($images = [], $nominations = []) {
		return [
			'id' => $this->id(),
			'name' => $this->val('user_name'),
			'counter_votes' => (int) $this->val('counter_votes'),
			'image_url' => isset($images[$this->val('image')]) ? (file_exists(APP_DIR_PATH . '/public/' . $images[$this->val('image')]->link('-160x160')) ? $images[$this->val('image')]->link('-160x160') : $images[$this->val('image')]->link('-160')) : null,
			'big_image_url' => isset($images[$this->val('image')]) ? $images[$this->val('image')]->link() : null,
			'nomination' => isset($nominations[$this->val('nomination_id')]) ? $nominations[$this->val('nomination_id')]['name'] : ''
		];
	}

}
