<?php

namespace App\Model;

class StatObject extends \Sky4\Model\Composite {

	const FIRM_SHOW = 1;
	const PRICE_SHOW = 2;
	const PRICE_LIST = 3;
	const PROMO_LIST = 4;
	const PROMO_SHOW = 5;
	const VIDEO_LIST = 6;
	const VIDEO_SHOW = 7;
	const REVIEW_LIST = 8;
	const FIRM_SHOW_URL_CLICK = 9;
	const FORM_FEEDBACK_OPEN = 10;
	const FORM_FEEDBACK_SEND = 11;
	const FORM_CALLBACK_OPEN = 12;
	const FORM_CALLBACK_SEND = 13;
	const FORM_PRICE_REQUEST_OPEN = 14;
	const FORM_PRICE_REQUEST_SEND = 15;
	const CATALOG_FIRM_LIST_FIRM = 16;
	const CATALOG_PRICE_LIST_FIRM = 17;
	const CATALOG_PRICE_LIST_PRICE = 18;
	const SEARCH_MAIN = 19;
	const SEARCH_FIRM_LIST = 20;
	const SEARCH_PRICE_LIST = 21;
	const ADVERT_MODULE_SHOW = 22;
	const ADVERT_MODULE_CLICK = 23;
	const PHONE_HIDER_CLICK = 24;
	const YML_URL_CLICK = 25;
	const FIRM_PHONE_CALL_CLICK = 26;

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
			1 => 'Показ карточки фирмы', //FIRM_SHOW;
			2 => 'Показ карточек товара', //PRICE_SHOW
			3 => 'Показ страниц прайс-листа', //PRICE_LIST;
			4 => 'Показ списка акций', //PROMO_LIST
			5 => 'Показ конкретной акции', //PROMO_SHOW
			6 => 'Показ списка видео', //VIDEO_LIST
			7 => 'Показ конкретного видео', //VIDEO_SHOW
			8 => 'Показ страницы отзывов', //REVIEW_LIST
			9 => 'Переход по ссылке на сайт фирмы', //FIRM_SHOW_URL_CLICK
			10 => 'Показ формы сообщения', //FORM_FEEDBACK_OPEN
			11 => 'Отправка сообщения', //FORM_FEEDBACK_SEND
			12 => 'Открытие формы обратного звонка', //FORM_CALLBACK_OPEN
			13 => 'Заявка на обратный звонок', //FORM_CALLBACK_SEND
			14 => 'Открытие формы отправки заказа', //FORM_PRICE_REQUEST_OPEN
			15 => 'Заказ товара/услуги', //FORM_PRICE_REQUEST_SEND
			16 => 'Показ фирмы в списках каталога фирм', //CATALOG_FIRM_LIST_FIRM
			17 => 'Показ фирмы в списках каталога товаров', //CATALOG_PRICE_LIST_FIRM
			18 => 'Показ товаров/услуг в списках каталога товаров', //CATALOG_PRICE_LIST_PRICE
			19 => 'Показ фирмы при поиске на странице краткого обзора', //SEARCH_MAIN
			20 => 'Показ фирмы в списке фирм при поиске на сайте', //SEARCH_FIRM_LIST
			21 => 'Показ товаров при поиске на сайте', //SEARCH_PRICE_LIST			
			22 => 'Показ рекламного модуля', //ADVERT_MODULE_SHOW
			23 => 'Переход по рекламному модулю', //ADVERT_MODULE_CLICK
			24 => 'Просмотрен блок контактов фирмы', //PHONE_HIDER_CLICK
			25 => 'Переход по внешней ссылке товара', //YML_URL_CLICK
			26 => 'Нажатие на номер телефона фирмы/звонок' //FIRM_PHONE_CALL_CLICK
		];
	}

	public static function getStatGroups() {
		return ['main_stat' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 24, 25], 'additional_stat' => [16, 17, 18, 19, 20, 21, 22, 23, 26]];
	}

	public static function getStatGroupNameByType($type) {
		$stat_groups = self::getStatGroups();
		return in_array($type, $stat_groups['main_stat']) ? 'main_stat' : 'additional_stat';
	}

	public static function getStatGroupNameByUrl($url) {
		return (strpos($url, '/firm/show/') !== false || strpos($url, '/price/show/') !== false) ? 'main_stat' : 'additional_stat';
	}

	public static function getTypesIdsForPopularity() {
		return [1, 2, 3, 5, 7, 8, 24];
	}

}
