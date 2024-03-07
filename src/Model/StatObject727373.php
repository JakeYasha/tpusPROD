<?php

namespace App\Model;
class StatObject727373 extends \Sky4\Model\Composite {

	const FIRM_SHOW = 1;
    const QUESTION_FIRM_LIST = 2;
	const QUESTION_PRICE_LIST = 3;
	const FIRM_SHOW_URL_CLICK = 4;
    const QUESTION_FIRM_LIST_URL_CLICK = 5;
    const QUESTION_PRICE_LIST_URL_CLICK = 6;
	const FORM_FEEDBACK_OPEN = 7;
	const FORM_FEEDBACK_SEND = 8;

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\NameTrait;

	public function fields() {
		return [
			'id_city' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'id_stat_user' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'id_stat_request' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'model_alias' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(50)],
			'model_id' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getInt(8)],
			'timestamp_inserting' => [
				'elem' => 'date_time_field',
				'label' => 'Время добавления'
			],
			'type' => ['col' => \Sky4\Db\ColType::getInt(8)]
		];
	}

	public function beforeInsert(&$vals = array()) {
		if (is_array($vals)) {
			$vals['timestamp_inserting'] = isset($vals['timestamp_inserting']) ? $vals['timestamp_inserting'] : \Sky4\Helper\DeprecatedDateTime::now();
		}
		return $this;
	}

	public static function getTypeNames() {
		return [
			1 => 'Показ карточки фирмы', //FIRM_SHOW
			2 => 'Показ фирмы на странице вопроса', //QUESTION_FIRM_LIST
			3 => 'Показ товара/услуги на странице вопроса', //QUESTION_PRICE_LIST
			4 => 'Переход по ссылке на сайт фирмы', //FIRM_SHOW_URL_CLICK
			5 => 'Переход по ссылке на сайт фирмы со страницы вопроса', //QUESTION_FIRM_LIST_URL_CLICK
			6 => 'Переход по ссылке на товар/услугу на сайте фирмы со страницы вопроса', //QUESTION_PRICE_LIST_URL_CLICK
			7 => 'Показ формы сообщения', //FORM_FEEDBACK_OPEN
			8 => 'Отправка сообщения', //FORM_FEEDBACK_SEND
		];
	}
    
   	public static function getStatGroups() {
		return ['main_stat' => [1, 3, 4, 5, 6, 7, 8], 'additional_stat' => [2]];
	}
	
	public function getTypesIdsForPopularity() {
		return [2, 3];
	}

	public static function getStatGroupNameByUrl($url) {
		return (strpos($url, '/firm/show/') !== false || strpos($url, '/price/show/') !== false) ? 'main_stat' : 'additional_stat';
	}
    
    public static function getStatGroupNameByType($type) {
		$stat_groups = self::getStatGroups();
		return in_array($type, $stat_groups['main_stat']) ? 'main_stat' : 'additional_stat';
	}

}
