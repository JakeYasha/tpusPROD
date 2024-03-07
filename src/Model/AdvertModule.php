<?php

namespace App\Model;

class AdvertModule extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\NameTrait,
	 Component\ImageTrait,
	 Component\FullImageTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait,
	 Component\TimestampIntervalTrait;

	public $_temp_type = null;
	public $restrictions = [];

	public function beforeUpdate(&$vals) {
		$_this = new AdvertModule($this->id());

		if (isset($vals['timestamp_beginning'])) {
			$vals['timestamp_beginning'] = $vals['timestamp_beginning'].' 03:00:00';
		}
		if (isset($vals['timestamp_ending'])) {
			$vals['timestamp_ending'] = $vals['timestamp_ending'].' 23:59:59';
		}

		$image = $_this->val('image');
		if ($image && isset($vals['image']) && $vals['image'] !== $image) {
			$images = \Sky4\Model\Utils::getObjectsByIds($image);
			if (isset($images[$image]) && $images[$image]->exists()) {
				$images[$image]->delete();
			}
		}

		$full_image = $_this->val('full_image');
		if ($full_image && isset($vals['full_image']) && $vals['full_image'] !== $full_image) {
			$images = \Sky4\Model\Utils::getObjectsByIds($full_image);
			if (isset($images[$full_image]) && $images[$full_image]->exists()) {
				$images[$full_image]->delete();
			}
		}
		return parent::beforeUpdate($vals);
	}

	public function afterInsert(&$vals, $parent_object = null) {
		$this->saveRels($vals);
		$images = \Sky4\Model\Utils::getObjectsByIds($this->val('image'));

		if (isset($images[$this->val('image')]) && $images[$this->val('image')] instanceof FirmFile && $images[$this->val('image')] && (int)$images[$this->val('image')]->val('flag_is_temp') === 1) {
			$images[$this->val('image')]->update(['flag_is_temp' => 0]);
		}

		$full_images = \Sky4\Model\Utils::getObjectsByIds($this->val('full_image'));
		if (isset($full_images[$this->val('full_image')]) && $full_images[$this->val('full_image')] instanceof FirmFile && $full_images[$this->val('full_image')] && (int)$full_images[$this->val('full_image')]->val('flag_is_temp') === 1) {
			$full_images[$this->val('full_image')]->update(['flag_is_temp' => 0]);
		}
		return parent::afterInsert($vals, $parent_object);
	}

	public function afterUpdate(&$vals) {
		$this->saveRels($vals);
		$images = \Sky4\Model\Utils::getObjectsByIds($this->val('image'));

		if (isset($images[$this->val('image')]) && $images[$this->val('image')] instanceof FirmFile && $images[$this->val('image')] && (int)$images[$this->val('image')]->val('flag_is_temp') === 1) {
			$images[$this->val('image')]->update(['flag_is_temp' => 0]);
		}

		$full_images = \Sky4\Model\Utils::getObjectsByIds($this->val('full_image'));
		if (isset($full_images[$this->val('full_image')]) && $full_images[$this->val('full_image')] instanceof FirmFile && $full_images[$this->val('full_image')] && (int)$full_images[$this->val('full_image')]->val('flag_is_temp') === 1) {
			$full_images[$this->val('full_image')]->update(['flag_is_temp' => 0]);
		}
		return parent::afterUpdate($vals);
	}

	public function cols() {
		return [
			'name' => ['label' => 'Название'],
			'type' => ['label' => 'Тип'],
			'id_firm' => ['label' => 'Фирма'],
			'timestamp_beginning' => ['label' => 'Время начала', 'style_class' => 'date-time'],
			'timestamp_ending' => ['label' => 'Время окончания', 'style_class' => 'date-time'],
			'flag_is_infinite' => ['label' => 'Бессрочный?', 'type' => 'flag'],
			'flag_is_commercial' => ['label' => 'На партнерской основе?', 'type' => 'flag'],
			'flag_is_active' => ['label' => 'На сайте?', 'type' => 'flag']
		];
	}

	public function defaultOrder() {
		return ['id' => 'DESC'];
	}

	public function fields() {
		return [
			'priority' => [
				'col' => \Sky4\Db\ColType::getInt(2),
				'elem' => 'text_field',
				'label' => 'Приоритет'
			],
			'type' => [
				'col' => \Sky4\Db\ColType::getList($this->types()),
				'elem' => 'drop_down_list',
				'label' => 'Тип рекламного модуля',
				'options' => $this->types(),
				'default_val' => 'wide_advert_module',
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'target_btn_name' => [
				'col' => \Sky4\Db\ColType::getList($this->target_btn_names()),
				'elem' => 'hidden_field',
				'label' => 'Текст кнопки'
			],
			'callback_btn_name' => [
				'col' => \Sky4\Db\ColType::getList($this->callback_btn_names()),
				'elem' => 'hidden_field',
				'label' => 'Текст кнопки'
			],
			'header' => [
				'col' => \Sky4\Db\ColType::getString(100),
				'elem' => 'text_field',
				'label' => 'Заголовок рекламного модуля (100 символов)',
				'params' => [
					'rules' => ['length' => ['max' => 100]]
				],
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'about_string' => [
				'col' => \Sky4\Db\ColType::getString(255),
				'elem' => 'text_area',
				'label' => 'Адреса компании (юр.название, юр.адрес, ОГРН, ИНН)',
				'params' => [
					'parser' => true
				],
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'adv_text' => [
				'col' => \Sky4\Db\ColType::getText(2),
				'elem' => 'text_area',
				'label' => 'Рекламный текст (255 символов)',
				'params' => [
					'rules' => ['length' => ['max' => 255]],
					'parser' => true
				],
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'url' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'hidden_field',
				'label' => 'Официальный сайт (главная страница)'
			],
			'email' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'text_field',
				'label' => 'Адрес электронной почты для уведомлений',
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'phone' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'text_field',
				'label' => 'Номер телефона для СМС уведомлений (+79106634350)',
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'more_url' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'text_field',
				'label' => 'Ссылка для перехода',
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'hidden_field',
				'label' => 'ID города'
			],
			'flag_is_commercial' => [
				'elem' => 'single_check_box',
				'label' => 'На партнерской основе?',
				'default_val' => 0
			],
			'flag_is_everywhere' => [
				'elem' => 'single_check_box',
				'label' => 'Показывать везде?',
				'default_val' => 0
			],
			'flag_is_infinite' => [
				'elem' => 'single_check_box',
				'label' => 'Постоянное предложение?',
				'default_val' => 0
			],
			'subgroup_ids' => [
				'col' => [
					'default_val' => '76',
					'flags' => 'not_null',
					'type' => 'string(1000)'
				],
				'elem' => 'model_autocomplete',
				'label' => 'Выбор подгрупп',
				'params' => [
					'model_alias' => 'price-catalog',
					'field_name' => 'web_many_name_for_subgroups',
					'default_field_name' => 'web_name',
					'rel_model_alias' => 'advert-module-group',
					'rel_field_name_1' => 'id_advert_module',
					'rel_field_name_2' => 'id_subgroup',
					'rel_model_field_id' => 'id_subgroup',
					'rel_model_conds' => ['where' => 'node_level = :node_level', 'params' => [':node_level' => 2]],
				]
			],
			'region_ids' => [
				'col' => [
					'default_val' => '76',
					'flags' => 'not_null',
					'type' => 'string(1000)'
				],
				'elem' => 'multiple_drop_down_list',
				'label' => 'Геотаргетинг',
				'options' => $this->getRegions(),
				'params' => [
					'multiple' => 'multiple'
				]
			],
			'firm_ids' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'type' => 'string(1000)'
				],
				'elem' => 'model_autocomplete',
				'label' => 'Выбор фирм',
				'params' => [
					'model_alias' => 'firm',
					'field_name' => 'name_for_firm_types',
					'rel_model_alias' => 'advert-module-firm',
					'rel_field_name_1' => 'id_advert_module',
					'rel_field_name_2' => 'id_firm'
				]
			],
			'firm_type_ids' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'type' => 'string(1000)'
				],
				'elem' => 'model_autocomplete',
				'label' => 'Выбор типов фирм',
				'params' => [
					'model_alias' => 'firm-type',
					'field_name' => 'name_for_firm_types',
					'rel_model_alias' => 'advert-module-firm-type',
					'rel_field_name_1' => 'id_advert_module',
					'rel_field_name_2' => 'id_firm_type',
					'rel_model_field_id' => 'id'
				]
			],
			'phones' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'text_field',
				'label' => 'Телефоны'
			],
			'total_views' => [
				'col' => \Sky4\Db\ColType::getInt(5),
				'elem' => 'hidden_field',
				'label' => 'Просмотры'
			],
			'total_clicks' => [
				'col' => \Sky4\Db\ColType::getInt(5),
				'elem' => 'hidden_field',
				'label' => 'Клики'
			]
		];
	}

	public function imageResolutions() {
		return [
			'full_image' => [
				['width' => 500]
			]
		];
	}

	public function filterFields() {
		return [
			'id_firm' => [
				'elem' => 'drop_down_list',
				'label' => 'Фирма',
				'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
				'field_name' => 'id_firm'
			],
			'flag_is_active' => [
				'elem' => 'single_check_box',
				'label' => 'Активные',
				'cond' => 'flag',
				'field_name' => 'flag_is_active',
				'assembler' => [
					'class_name' => '\\App\\Model\\Banner',
					'method_name' => 'assembleFilterActiveConds'
				]
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'flag_is_active']
		];
	}

	public function formStructure() {
		$firm = new Firm();
		$firm->getByIdFirm($this->id_firm());
		return [
			['type' => 'component', 'name' => 'Name'],
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'id_city'],
			['type' => 'component', 'name' => 'TimestampInterval'],
			['type' => 'component', 'name' => 'Active'],
			['type' => 'field', 'name' => 'flag_is_commercial'],
			['type' => 'field', 'name' => 'flag_is_infinite'],
			['type' => 'field', 'name' => 'priority'],
			//
			['type' => 'tab', 'name' => 'content', 'label' => 'Контент'],
			//['type' => 'field', 'name' => 'type', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'full_image', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'header', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'adv_text', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'url', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'more_url', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'phones', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'about_string', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'email', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'phone', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'subgroup_ids', 'tab_name' => 'content'],
			['type' => 'field', 'name' => 'region_ids', 'tab_name' => 'content'],
				//['type' => 'tab', 'name' => 'rubrics', 'label' => 'Контекстная реклама в КФ'],
				//['type' => 'field', 'name' => 'firm_ids', 'tab_name' => 'rubrics'],
				//['type' => 'field', 'name' => 'firm_type_ids', 'tab_name' => 'rubrics'],
				//['type' => 'field', 'name' => 'image', 'tab_name' => 'rubrics'],
				//['type' => 'field', 'name' => 'flag_is_everywhere', 'tab_name' => 'rubrics'],
		];
	}

	public function getImage() {
		$images = \Sky4\Model\Utils::getObjectsByIds($this->val('image'));
		$file = '';
		if ($this->hasImage()) {
			if ($images[$this->val('image')] instanceof FirmFile && $images[$this->val('image')]) {
				$file = new FirmFile();
				$file->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId($this->val('image')))[1]);
			} else if ($images[$this->val('image')] instanceof File && $images[$this->val('image')]) {
				$file = new File();
				$file->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId($this->val('image')))[1]);
			} else {
				$file = new File();
				$file->get(EMPTY_IMAGE_FILEID_STUB);
			}
		} else {
			$file = new File();
			$file->get(EMPTY_IMAGE_FILEID_STUB);
		}

		return $file;
	}

	public function getFullImage() {
		$images = \Sky4\Model\Utils::getObjectsByIds($this->val('full_image'));
		$file = '';
		if ($this->hasFullImage()) {
			if (isset($images[$this->val('full_image')]) && $images[$this->val('full_image')] instanceof FirmFile && $images[$this->val('full_image')]) {
				$file = new FirmFile();
				$file->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId($this->val('full_image')))[1]);
			} elseif (isset($images[$this->val('full_image')]) && $images[$this->val('full_image')] instanceof File && $images[$this->val('full_image')]) {
				$file = new File();
				$file->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId($this->val('full_image')))[1]);
			} else {
				$file = new File();
				$file->get(EMPTY_IMAGE_FILEID_STUB);
			}
		} else {
			$file = new File();
			$file->get(EMPTY_IMAGE_FILEID_STUB);
		}

		return $file;
	}

	public function hasImage() {
		return $this->val('image') != '';
	}

	public function hasEmail() {
		return $this->val('email') != '';
	}

	public function email() {
		return $this->val('email');
	}

	public function hasPhone() {
		return $this->val('phone') != '';
	}

	public function hasPhones() {
		return $this->val('phones') != '';
	}

	public function phone() {
		return $this->val('phone');
	}

	public function hasFullImage() {
		return $this->val('full_image') != '';
	}

	public function hasUrl() {
		return $this->val('url') != '';
	}

	public function hasMoreUrl() {
		return $this->val('more_url') != '';
	}

	private function getRegions() {
		$sr = new StsRegionCountry();
		return $sr->reader()
						->setWhere(['AND', 'id_region_country != :nil'], [':nil' => 0])
						->setOrderBy('name ASC')
						->getList();
	}

	public function orderableFieldsNames() {
		return [
			'timestamp_beginning',
			'timestamp_ending',
			'flag_is_active',
			'name',
		];
	}

	private function saveRels(&$vals) {
		$fields = $this->getFields();
		if (isset($fields['subgroup_ids']) && isset($fields['subgroup_ids']['elem']) && ($fields['subgroup_ids']['elem'] === 'model_autocomplete')) {
			$elem = \Sky4\Utils::getElemClass($fields['subgroup_ids']['elem']);
			$elem->setModel($this)
					->setParams(isset($fields['subgroup_ids']['params']) ? $fields['subgroup_ids']['params'] : [])
					->saveRels('subgroup_ids', $fields['subgroup_ids'], $vals);
		}

		if (isset($fields['region_ids']) && isset($vals['region_ids'])) {
			$br = new AdvertModuleRegion();
			$br->deleteAll(['AND', 'id_advert_module = :id_advert_module'], null, null, null, [':id_advert_module' => $this->id()]);

			$exploaded_vals = explode(',', $vals['region_ids']);
			foreach ($exploaded_vals as $id_region) {
				$br = new AdvertModuleRegion();
				$br->insert(['id_advert_module' => $this->id(), 'id_region' => $id_region]);
			}
		}

		if (isset($fields['firm_type_ids']) && isset($fields['firm_type_ids']['elem']) && ($fields['firm_type_ids']['elem'] === 'model_autocomplete')) {
			$elem = \Sky4\Utils::getElemClass($fields['firm_type_ids']['elem']);
			$elem->setModel($this)
					->setParams(isset($fields['firm_type_ids']['params']) ? $fields['firm_type_ids']['params'] : [])
					->saveRels('firm_type_ids', $fields['firm_type_ids'], $vals);
		}

		//app()->system()->reindex(SPHINX_BANNER_INDEX);
	}

	public static function prepare(AdvertModule $item, $image = null, $full_image = null) {
		$id_firm = $item->firm()->id();
		$id_service = $item->firm()->id_service();

		$adv_text = preg_replace_callback('~href="([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
			return 'target="_blank" rel="nofollow" href="'.app()->away(trim($matches[1]), $id_firm).'"';
		}, $item->val('adv_text'));

		return [
			'id' => $item->id(),
			'name' => $item->val('name'),
			'header' => $item->val('header'),
			'time_beginning' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_beginning')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_beginning'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_beginning')),
			'time_beginning_short' => date("d.m.Y", \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_beginning'))),
			'time_ending' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_ending')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_ending'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_ending')),
			'time_ending_short' => date("d.m.Y", \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_ending'))),
			'text' => $adv_text,
			'flag_is_commercial' => $item->val('flag_is_commercial'),
			'flag_is_everywhere' => $item->val('flag_is_everywhere'),
			'flag_is_infinite' => $item->val('flag_is_infinite'),
			'image' => $image,
			'full_image' => $full_image,
			'link' => app()->linkFilter($item->firm()->link(), ['mode' => 'advert-module', 'id_advert_module' => $item->id()]),
			'firm' => $item->firm(),
			'url' => $item->val('url'),
			'more_url' => $item->val('more_url'),
			'about_string' => $item->val('about_string'),
			'total_views' => $item->val('total_views'),
			'total_clicks' => $item->val('total_clicks'),
			'is_active' => /* \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_beginning')) < time() && */\Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_ending')) > time()
		];
	}

	public function delete() {
		$objects = \Sky4\Model\Utils::getObjectsByIds($this->val('image'));
		if (isset($objects[$this->val('image')]) && $objects[$this->val('image')]->exists()) {
			$objects[$this->val('image')]->delete();
		}

		$objects = \Sky4\Model\Utils::getObjectsByIds($this->val('full_image'));
		if (isset($objects[$this->val('full_image')]) && $objects[$this->val('full_image')]->exists()) {
			$objects[$this->val('full_image')]->delete();
		}

		$amf = new AdvertModuleFirm();
		$all = $amf->reader()
				->setWhere(['AND', 'id_advert_module = :id'], [':id' => $this->id()])
				->objects();

		foreach ($all as $ob) {
			$ob->delete();
		}

		$amft = new AdvertModuleFirmType();
		$all = $amft->reader()
				->setWhere(['AND', 'id_advert_module = :id'], [':id' => $this->id()])
				->objects();

		foreach ($all as $ob) {
			$ob->delete();
		}
		$amg = new AdvertModuleGroup();
		$all = $amg->reader()
				->setWhere(['AND', 'id_advert_module = :id'], [':id' => $this->id()])
				->objects();
		foreach ($all as $ob) {
			$ob->delete();
		}

		$amr = new AdvertModuleRegion();
		$all = $amr->reader()->setWhere(['AND', 'id_advert_module = :id'], [':id' => $this->id()])
				->objects();
		foreach ($all as $ob) {
			$ob->delete();
		}

		return parent::delete();
	}

	public function header() {
		return $this->exists() ? $this->val('header') : '';
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Рекламные модули';
	}

	public function types() {
		return [
			'' => 'Праздничный рекламный модуль',
			'wide_advert_module' => 'Широкий рекламный модуль',
			'default_advert_module' => 'Стандартный рекламный модуль'
		];
	}

	public function target_btn_names() {
		return [
			'more' => 'Подробнее',
			'promocode' => 'Получить промокод',
			'onlineshop' => 'В интернет-магазин'
		];
	}

	public function callback_btn_names() {
		return [
			'order' => 'Заказать',
			'join' => 'Записаться'
		];
	}

	public function isEverywhere() {
		return (int)$this->val('flag_is_everywhere') === 1 ? true : false;
	}

	public function getActiveAdvertModuleIds() {
		$amr = new AdvertModuleRegion();
		$am_ids = $amr->reader()
				->setSelect('id_advert_module')
				->setWhere(['AND', 'id_region = :id_region'], [':id_region' => app()->location()->getRegionId()])
				->rowsWithKey('id_advert_module');
		$_where = ['AND', 'flag_is_active = :flag_is_active', 'timestamp_ending > :now'];
		$_params = [':flag_is_active' => 1, ':now' => \Sky4\Helper\DeprecatedDateTime::now()];
		if (count($am_ids) > 0) {
			$conds_am_id = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id');
			$_where[] = $conds_am_id['where'];
			$_params = $_params + $conds_am_id['params'];
		} else {
			return [];
		}
		return array_keys($this->reader()
						->setSelect(['id'])
						->setWhere($_where, $_params)
						->rowsWithKey('id'));
	}

}
