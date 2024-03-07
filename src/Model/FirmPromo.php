<?php

namespace App\Model;

class FirmPromo extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\ActiveTrait,
	 Component\TimestampActionTrait,
	 Component\TimestampIntervalTrait,
	 Component\NameTrait,
	 Component\NewStateTrait,
	 Component\ExtendedTextTrait,
	 Component\ImageTrait;

	public function cols() {
		$cols = [
			'name' => ['label' => 'Название'],
			'id_firm' => ['label' => 'Фирма'],
			'flag_is_infinite' => ['label' => 'Бессрочная', 'type' => 'flag'],
			'flag_is_active' => ['label' => 'На сайте', 'type' => 'flag'],
		];

		$cols = $cols + $this->timestampActionComponent()->cols('timestamp_last_updating') + $this->newComponent()->cols() + $this->activeComponent()->cols();

		return $cols;
	}

	public function defaultOrder() {
		return ['timestamp_last_updating' => 'DESC'];
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function fields() {
		return [
			'phone' => [
				'col' => \Sky4\Db\ColType::getString(255),
				'elem' => 'text_field',
				'label' => 'Телефон для справок',
				'params' => [
					'rules' => ['required']
				]
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'hidden_field',
				'label' => 'ID города'
			],
			'flag_is_infinite' => [
				'elem' => 'single_check_box',
				'label' => 'Бессрочная акция',
				'default_val' => 0
			],
			'catalog_ids' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'type' => 'string(255)'
				],
				'elem' => 'model_autocomplete',
				'label' => 'Рубрикатор',
				'params' => [
					'model_alias' => 'price-catalog',
					'field_name' => 'web_many_name',
					'rel_model_alias' => 'firm-promo-catalog',
					'rel_field_name_1' => 'firm_promo_id',
					'rel_field_name_2' => 'price_catalog_id',
					'rel_model_field_id' => 'id'
				],
				'attrs' => []
			],
			'flag_is_present' => [
				'elem' => 'single_check_box',
				'label' => 'Подарок?',
				'default_val' => 0
			],
			'percent_value' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'Величина скидки (%)',
				'params' => [
					'rules' => ['int']
				]
			],
			'total_views' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_8'
				],
				'elem' => 'hidden_field',
				'label' => 'Количество просмотров'
			],
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

	public function imageResolutions() {
		return [
			'image' => [
				['width' => 750],
				['width' => 150, 'height' => 150]
			]
		];
	}

	public function isActual() {
		return /* \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_beginning')) <= time() && */ \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_ending')) >= time();
	}

	public function formStructure() {
		$firm = new Firm();
		$firm->getByIdFirm($this->id_firm());
		return [
			['type' => 'component', 'name' => 'Name'],
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'id_city'],
			['type' => 'field', 'name' => 'phone'],
			['type' => 'component', 'name' => 'TimestampInterval'],
			['type' => 'field', 'name' => 'percent_value'],
			['type' => 'field', 'name' => 'flag_is_present'],
			['type' => 'field', 'name' => 'flag_is_infinite'],
			['type' => 'field', 'name' => 'flag_is_active'],
			['type' => 'field', 'name' => 'image'],
			//
			['type' => 'tab', 'name' => 'texts', 'label' => 'Тексты'],
			['type' => 'field', 'name' => 'brief_text', 'tab_name' => 'texts'],
			['type' => 'field', 'name' => 'text', 'tab_name' => 'texts'],
			//
			['type' => 'tab', 'name' => 'rubrics', 'label' => 'Рубрикатор'],
			['type' => 'field', 'name' => 'catalog_ids', 'tab_name' => 'rubrics'],
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Акции';
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		$vals['flag_is_new'] = '1';
		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		if (count($vals) != 1) {
			$vals['flag_is_new'] = '0';
			$image = $this->val('image');
			if ($image && isset($vals['image']) && $vals['image'] !== $image) {
				$images = \Sky4\Model\Utils::getObjectsByIds($image);
				if (isset($images[$image]) && $images[$image]->exists()) {
					$images[$image]->delete();
				}
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
		return parent::afterInsert($vals, $parent_object);
	}

	public function afterUpdate(&$vals) {
		$images = \Sky4\Model\Utils::getObjectsByIds($this->val('image'));
		if (isset($images[$this->val('image')]) && $images[$this->val('image')] instanceof FirmFile && $images[$this->val('image')] && (int)$images[$this->val('image')]->val('flag_is_temp') === 1) {
			$images[$this->val('image')]->update(['flag_is_temp' => 0]);
		}
		$this->saveRels($vals);
		return parent::afterUpdate($vals);
	}

	private function saveRels(&$vals) {
		$fields = $this->getFields();
		if (isset($fields['catalog_ids']) && isset($fields['catalog_ids']['elem'])) {
			$elem = \Sky4\Utils::getElemClass($fields['catalog_ids']['elem']);
			$elem->setModel($this)
					->setParams(isset($fields['catalog_ids']['params']) ? $fields['catalog_ids']['params'] : [])
					->saveRels('catalog_ids', $fields['catalog_ids'], $vals);
		}
	}

	public function link() {
		return '/firm/show/'.$this->firm()->id_firm().'/'.$this->firm()->id_service().'/?id_promo='.$this->id().'&mode=promo';
	}

	public function orderableFieldsNames() {
		$cols = $this->cols();
		return array_keys($cols);
	}

	public static function getTextWithObfuscatedLinks(FirmPromo $item) {
		$order = array("\r\n", "\n", "\r");
		$text = $item->val('text');
		$id_firm = $item->firm()->id_firm();
		$id_service = $item->firm()->id_service();

		$text = str()->replace(str()->replace($text, ' target="_blank"', ''), ' rel="nofollow"', '');
		$text = preg_replace_callback('~href="([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
			return 'target="_blank" rel="nofollow" href="'.app()->away(trim($matches[1]), $id_firm).'"';
		}, $text);

		return str()->replace($text, '\\', '');
	}

	public static function prepare(FirmPromo $item, $image = null, $big_image = null) {
		$id_firm = $item->id_firm();
		$id_service = $item->firm()->id_service();

		$firm = new Firm();
		$firm->getByIdFirm($id_firm);

		$brief_text = preg_replace_callback('~href="([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
			return 'target="_blank" rel="nofollow" href="'.app()->away(trim($matches[1]), $id_firm).'"';
		}, $item->val('brief_text'));

		return [
			'id' => $item->id(),
			'id_firm' => $item->firm()->id_firm(),
			'id_service' => $item->firm()->id_service(),
			'name' => $item->val('name'),
			'time_beginning' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_beginning')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_beginning'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_beginning')),
			'time_beginning_short' => date("d.m.Y", \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_beginning'))),
			'time_ending' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_ending')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_ending'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_ending')),
			'time_ending_short' => date("d.m.Y", \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_ending'))),
			'text' => $brief_text,
			'big_text' => FirmPromo::getTextWithObfuscatedLinks($item),
			'flag_is_infinite' => $item->val('flag_is_infinite'),
			'flag_is_present' => $item->val('flag_is_present'),
			'percent_value' => $item->val('percent_value'),
			'image' => $image,
			'big_image' => $big_image,
			'link' => app()->linkFilter($firm->link(), ['mode' => 'promo', 'id_promo' => $item->id()]),
			'firm' => $firm,
            'firm_name' => $firm->name(),
			'phone' => $item->val('phone'),
			'is_active' => /* \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_beginning')) < time() && */\Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_ending')) > time(),
			'total_views' => (int)$item->val('total_views')
		];
	}

	public function delete() {
		$objects = \Sky4\Model\Utils::getObjectsByIds($this->val('image'));
		if (isset($objects[$this->val('image')]) && $objects[$this->val('image')]->exists()) {
			$objects[$this->val('image')]->delete();
		}

		$fpc = new FirmPromoCatalog();
		$all = $fpc->reader()
				->setWhere(['AND', 'firm_promo_id = :id'], [':id' => $this->id()])
				->objects();

		foreach ($all as $ob) {
			$ob->delete();
		}

		return parent::delete();
	}

	public function getActivePromoIds() {
		$conds_city_id = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');

		$_where = [
			'AND',
			//['OR',
			//'`flag_is_infinite` = :flag',
			['AND', /* '`timestamp_beginning`< :now', */ 'timestamp_ending > :now'],
			//],
			['AND', 'flag_is_active = :flag', $conds_city_id['where']]
		];

		$_params = array_merge([':flag' => 1, ':now' => \Sky4\Helper\DeprecatedDateTime::now()], $conds_city_id['params']);

		return array_keys($this->reader()
						->setSelect(['id'])
						->setWhere($_where, $_params)
						->rowsWithKey('id'));
	}

}
