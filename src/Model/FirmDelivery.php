<?php

namespace App\Model;

class FirmDelivery extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\NewStateTrait;

	public function beforeInsert(&$vals, $parent_object = null) {
		$vals['flag_is_new'] = '1';
		self::checkboxesCheck($vals);
		self::clearLinks($vals);
		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		$vals['flag_is_new'] = '0';
		self::checkboxesCheck($vals);
		self::clearLinks($vals);
		return parent::beforeUpdate($vals);
	}

	public static function checkboxesCheck(&$vals) {
		if (!isset($vals['type'])) {
			$vals['type'] = '';
		} elseif (is_array($vals['type'])) {
			$vals['type'] = implode(',', $vals['type']);
		}
	}

	public function cols() {
		$cols = [
			'id_firm' => ['label' => 'Фирма'],
		];

		$cols = $cols + $this->timestampActionComponent()->cols('timestamp_last_updating') + $this->newComponent()->cols();

		return $cols;
	}

	public function defaultOrder() {
		return ['timestamp_inserting' => 'DESC'];
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function fields() {
		return [
			'type' => [
				'col' => \Sky4\Db\ColType::getString(300),
				'elem' => 'check_boxes',
				'label' => 'Вид доставки',
				'options' => self::types()
			]
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
			['type' => 'field', 'name' => 'flag_is_new']
		];
	}

	public function orderableFieldsNames() {
		$cols = $this->cols();
		return array_keys($cols);
	}

	public function link() {
		return $this->firm()->linkItem();
	}

	public static function types() {
		return [
			1 => 'Курьерская доставка',
			2 => 'Срочная курьерская доставка',
			3 => 'Штатный курьер',
			4 => 'Самовывоз из офиса, магазина',
			5 => 'Самовывоз из пункта выдачи заказов (ПВЗ)',
			6 => 'Постомат',
			7 => 'Почта России',
			8 => 'Транспортная компания'
		];
	}

	public static function clearLinks(&$vals) {
		if (isset($vals['text']) && $vals['text']) {
			$vals['text'] = preg_replace_callback('~href="([^"]+)"~u', function($matches) {
				$url_parts = parse_url(trim($matches[1]));
				if (str()->pos($url_parts['path'], '/page/away/') === false) {
					$url = '';
					if (isset($url_parts['host']) && $url_parts['host'] !== 'www.tovaryplus.ru') {
						$url = app()->away('http://'.$url_parts['host'].$url_parts['path']);
						if (isset($url_parts['query'])) {
							$url .= '?'.$url_parts['query'];
						}
						return 'target="_blank" rel="nofollow" href="'.$url.'"';
					} else {
						$url = $url_parts['path'];
						if (isset($url_parts['query'])) {
							$url .= '?'.$url_parts['query'];
						}
						return 'href="'.$url.'"';
					}
				} else {
					return 'href="'.trim($matches[1]).'"';
				}
			}, $vals['text']);
		}
	}

	public function title() {
		return $this->exists() ? 'Доставка #'.$this->id() : 'Доставка';
	}

}