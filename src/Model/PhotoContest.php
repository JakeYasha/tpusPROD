<?php

namespace App\Model;
class PhotoContest extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\NameTrait,
	 Component\ExtendedTextTrait,
	 Component\ImageTrait,
	 Component\TimestampIntervalTrait,
	 Component\TimestampActionTrait,
	 Component\MetadataTrait;

	public function cols() {
		$cols = $this->nameComponent()->cols() + $this->timestampIntervalComponent()->cols() + $this->activeComponent()->cols() + [
			'flag_is_working' => ['label' => 'Действующий конкурс', 'type' => 'flag'],
		];

		return $cols;
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function fields() {
		return [
			'banner_ids' => [
				'col' => \Sky4\Db\ColType::getString(1000),
				'elem' => 'multiple_drop_down_list',
				'label' => 'Баннеры внтури фото-конкурса',
				'options' => APP_SUB_SYSTEM_NAME === 'CMS' ? \Sky4\Container::getList('Banner') : []
			],
			'sponsor_name' => [
				'elem' => 'text_field',
				'label' => 'Назание спонсора',
				'col' => \Sky4\Db\ColType::getString(255)
			],
			'sponsor_url' => [
				'elem' => 'text_field',
				'label' => 'Ссылка на спонсора',
				'col' => \Sky4\Db\ColType::getString(255)
			],
			'flag_is_working' => [
				'elem' => 'single_check_box',
				'label' => 'Действующий конкурс',
				'default_val' => 0
			],
			'text_rights' => [
				'elem' => 'tiny_mce',
				'label' => 'Правила голосования',
				'col' => \Sky4\Db\ColType::getText(2),
				'params' => [
					'parser' => true
				]
			],
			'text_terms' => [
				'elem' => 'tiny_mce',
				'label' => 'Условия проведения конкурса',
				'col' => \Sky4\Db\ColType::getText(2),
				'params' => [
					'parser' => true
				]
			],
			'text_prizes' => [
				'elem' => 'tiny_mce',
				'label' => 'Призы',
				'col' => \Sky4\Db\ColType::getText(2),
				'params' => [
					'parser' => true
				]
			],
			'text_winner' => [
				'elem' => 'tiny_mce',
				'label' => 'Поздравительный текст',
				'col' => \Sky4\Db\ColType::getText(2),
				'params' => [
					'parser' => true
				]
			],
			'nomination_ids' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'string(1000)'
				],
				'elem' => 'multiple_drop_down_list',
				'label' => 'Номинации',
				'options' => \Sky4\Container::getList('PhotoContestNomination'),
				'params' => [
					'multiple' => 'multiple'
				]
			],
		];
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		if (isset($vals['banner_ids'])) {
			$_banner_ids = explode(',', $vals['banner_ids']);
			$banner_ids = [];
			foreach ($_banner_ids as $id) {
				if ((int) $id !== 0) {
					$banner_ids[] = (int) $id;
				}
			}
		}
		$vals['banner_ids'] = implode(',', $banner_ids);
		return parent::beforeInsert($vals);
	}

	public function beforeUpdate(&$vals) {
		if (isset($vals['banner_ids'])) {
			$_banner_ids = explode(',', $vals['banner_ids']);
			$banner_ids = [];
			foreach ($_banner_ids as $id) {
				if ((int) $id !== 0) {
					$banner_ids[] = (int) $id;
				}
			}
			$vals['banner_ids'] = implode(',', $banner_ids);
		}

		return parent::beforeUpdate($vals);
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'Name'],
			['type' => 'component', 'name' => 'Image'],
			['type' => 'field', 'name' => 'sponsor_name'],
			['type' => 'field', 'name' => 'sponsor_url'],
			['type' => 'field', 'name' => 'banner_ids'],
			['type' => 'label', 'text' => 'Флаги'],
			['type' => 'field', 'name' => 'flag_is_working'],
			['type' => 'field', 'name' => 'flag_is_active'],
			['type' => 'component', 'name' => 'TimestampInterval'],
			//
			['type' => 'tab', 'name' => 'texts', 'label' => 'Тексты'],
			['type' => 'component', 'name' => 'ExtendedText', 'tab_name' => 'texts'],
			['type' => 'field', 'name' => 'text_rights', 'tab_name' => 'texts'],
			['type' => 'field', 'name' => 'text_terms', 'tab_name' => 'texts'],
			['type' => 'field', 'name' => 'text_prizes', 'tab_name' => 'texts'],
			['type' => 'field', 'name' => 'text_winner', 'tab_name' => 'texts'],
			//
			['type' => 'tab', 'name' => 'nomination', 'label' => 'Номинации'],
			['type' => 'field', 'name' => 'nomination_ids', 'tab_name' => 'nomination'],
			//
			['type' => 'tab', 'name' => 'meta', 'label' => 'Метаданные'],
			['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'meta'],
		];
	}

	public function isWorking() {
		return (int) $this->val('flag_is_working') === 1;
	}

	public function isFinished() {
		return \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_ending')) < time();
	}

	public function isHasWinner() {
		$pci = new PhotoContestItem();
		$pci->reader()
				->setWhere(['AND', 'photo_contest_id = :photo_contest_id', 'flag_is_winner = :flag_is_winner'], ['photo_contest_id' => $this->id(), ':flag_is_winner' => 1])
				->objectByConds();

		return \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_ending')) < time() && $pci->exists();
	}

	public function imageResolutions() {
		return [
			'image' => [
				['width' => 270, 'height' => 170]
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Фото-конкурс';
	}

	public function getListOfFields($scope = 'list') {
		if ($scope === 'list') {
			$res = ['id', 'name', 'timestamp_beginning', 'timestamp_ending', 'flag_is_working', 'flag_is_active', 'brief_text', 'sponsor_name', 'sponsor_url', 'image'];
		} else {
			$res = '*';
		}

		return $res;
	}

	public function prepare($scope = 'list', $images = []) {
		$timestamp_start = \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_beginning'));
		$timestamp_end = \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_ending'));
		$banner = new Banner();
		$banners = [];
		if ($this->val('banner_ids')) {
			$_banner_ids = explode(',', $this->val('banner_ids'));
			shuffle($_banner_ids);
			$_banner_ids = array_slice($_banner_ids, 0, 3);
			$_banners = $banner->reader()->objectsByIds($_banner_ids);
			foreach ($_banners as $ban) {
				if (!isset($banners[$ban->val('type')])) {
					$banners[$ban->val('type')] = [];
				}
				$banners[$ban->val('type')][] = $ban;
			}
		}
		$fields = [
			'id' => $this->id(),
			'name' => $this->name(),
			'brief_text' => str()->replace($this->val('brief_text'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()]),
			'text' => str()->replace($this->val('text'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()]),
			'text_winner' => str()->replace($this->val('text_winner'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()]),
			'date_start' => date('d', $timestamp_start) . ' ' . \CMonth::name(date('m', $timestamp_start), 1) . ' ' . date('Y', $timestamp_start),
			'date_end' => date('d', $timestamp_end) . ' ' . \CMonth::name(date('m', $timestamp_end), 1) . ' ' . date('Y', $timestamp_end),
			'sponsor_name' => $this->val('sponsor_name'),
			'sponsor_url' => trim($this->val('sponsor_url')),
			'working' => $this->isWorking(),
			'finished' => $this->isFinished(),
			'has_winner' => $this->isHasWinner(),
			'link' => $this->link(),
			'link_add' => '/photo-contest/add-photo/' . $this->id() . '/',
			'banners' => $banners
		];

		$fields['image_url'] = isset($images[$this->val('image')]) ? $images[$this->val('image')]->link('-270x170') : false;

		if ($scope !== 'list') {
			$fields += [
				'text' => $this->val('text')
			];
		}

		return $fields;
	}

	public function getNominations($params = []) {
		$nominations = [];
		if (!isset($params['nomination'])) {
			$params['nomination'] = null;
		}
		if ($this->val('nomination_ids')) {
			$nomination_ids = explode(',', $this->val('nomination_ids'));
			if ($nomination_ids) {
				$pn = new PhotoContestNomination();
				$_nominations = $pn->reader()->objectsByIds($nomination_ids);

				$i = 0;
				foreach ($_nominations as $nomination) {
					$i++;
					$image = $nomination->imageComponent()->get();
					$nominations[(int) $nomination->id()] = [
						'id' => (int) $nomination->id(),
						'name' => $nomination->name(),
						'image_url' => $image->exists() ? $image->link('-270x170') : false,
						'link' => app()->linkFilter($this->link(), $params, ['nomination' => $nomination->id()]),
						'active' => $params['nomination'] === (int) $nomination->id() ? true : (($params['nomination'] === null && $i === 1) ? true : false)
					];
				}
			}
		}

		return $nominations;
	}

	public static function userCanVote($photo_contest_id, $nomination_id) {
		$vote = new PhotoContestItemVote();
		return $vote->check($photo_contest_id, $nomination_id);
	}

}
