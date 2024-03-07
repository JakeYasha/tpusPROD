<?php

define('AMMAP_PATH', APP_DIR_PATH . '/public/plugins/ammap/');

class ACrontabSiteStatistics extends ACrontabAction {

	public function run() {
		//$this->updateAmmapXml();
		$this->log('заполнение таблицы stat_site');
		$this->updateStatSite();
		$this->log('обновлено строк: 1');
		//здесь будет сброс статистики в архив и очистка таблиц
		//$this->log('обновление flash-карты');
		//$this->updateAmmapXml();
		//$this->log('завершено');
	}

	private function getTotalVisitors() {
		$ym = new YandexMetrika();
		$stat = $ym->setToken(YANDEX_METRIKA_TOKEN)
				->setCounter(YANDEX_METRIKA_COUNTER)
				->getPeriod(date('Ymd', mktime(0, 0, 0, date('m'), date('d') - 30, date('Y'))), date('Ymd'));

		return $stat['www.tovaryplus.ru']['visitors'];
	}

	private function updateAmmapXml() {

		$xmlmap = array(
			"name" => "map",
			"map_file" => "maps/russia.swf",
			"tl_long" => "19.649611",
			"tl_lat" => "81.851927",
			"br_long" => "191.009712",
			"br_lat" => "41.196582",
			"zoom" => "199.9997%",
			"zoom_x" => "17.45%",
			"zoom_y" => "-100.65%");

		$areacodes = [
			1 => ["reg_code" => "RU_AD", "title" => "Адыгея республика"],
			22 => ["reg_code" => "RU_ALT", "title" => "Алтайский край"],
			4 => ["reg_code" => "RU_AL", "title" => "Алтай республика (ГОРНЫЙ АЛТАЙ)"],
			28 => ["reg_code" => "RU_AMU", "title" => "Амурская область"],
			29 => ["reg_code" => "RU_ARK", "title" => "Архангельская область"],
			30 => ["reg_code" => "RU_AST", "title" => "Астраханская область"],
			2 => ["reg_code" => "RU_BA", "title" => "Башкортостан республика"],
			31 => ["reg_code" => "RU_BEL", "title" => "Белгородская область"],
			32 => ["reg_code" => "RU_BRY", "title" => "Брянская область"],
			3 => ["reg_code" => "RU_BU", "title" => "Бурятия республика"],
			33 => ["reg_code" => "RU_VLA", "title" => "Владимирская область"],
			34 => ["reg_code" => "RU_VGG", "title" => "Волгоградская область"],
			35 => ["reg_code" => "RU_VLG", "title" => "Вологодская область"],
			36 => ["reg_code" => "RU_VOR", "title" => "Воронежская область"],
			5 => ["reg_code" => "RU_DA", "title" => "Дагестан республика"],
			79 => ["reg_code" => "RU_YEV", "title" => "Еврейская автономная область"],
			501 => ["reg_code" => "RU_ZAB", "title" => "Забайкальский край"],
			38 => ["reg_code" => "RU_IRK", "title" => "Иркутская область"],
			37 => ["reg_code" => "RU_IVA", "title" => "Ивановская область"],
			6 => ["reg_code" => "RU_IN", "title" => "Ингушская республика"],
			7 => ["reg_code" => "RU_KB", "title" => "Кабардино-Балкария республика"],
			39 => ["reg_code" => "RU_KGD", "title" => "Калининградская область"],
			8 => ["reg_code" => "RU_KL", "title" => "Калмыкия республика"],
			40 => ["reg_code" => "RU_KLU", "title" => "Калужская область"],
			41 => ["reg_code" => "RU_KAM", "title" => "Камчатская область"],
			9 => ["reg_code" => "RU_KC", "title" => "Карачаево-Черкесия респулика"],
			10 => ["reg_code" => "RU_KR", "title" => "Карелия республика"],
			42 => ["reg_code" => "RU_KEM", "title" => "Кемеровская область"],
			43 => ["reg_code" => "RU_KIR", "title" => "Кировская область"],
			11 => ["reg_code" => "RU_KO", "title" => "Коми республика"],
			44 => ["reg_code" => "RU_KOS", "title" => "Костромская область"],
			23 => ["reg_code" => "RU_KDA", "title" => "Краснодарский край"],
			24 => ["reg_code" => "RU_KYA", "title" => "Красноярский край"],
			502 => ["reg_code" => "RU_KGN", "title" => "Курганская область"],
			46 => ["reg_code" => "RU_KRS", "title" => "Курская область"],
			78 => ["reg_code" => "RU_LEN", "title" => "Ленинградская область"],
			48 => ["reg_code" => "RU_LIP", "title" => "Липецкая область"],
			49 => ["reg_code" => "RU_MAG", "title" => "Магаданская область"],
			12 => ["reg_code" => "RU_ME", "title" => "Марий Эл республика"],
			13 => ["reg_code" => "RU_MO", "title" => "Мордовия республика"],
			503 => ["reg_code" => "RU_MOW", "title" => "Москва"],
			77 => ["reg_code" => "RU_MOS", "title" => "Московская область"],
			51 => ["reg_code" => "RU_MUR", "title" => "Мурманская область"],
			83 => ["reg_code" => "RU_NEN", "title" => "Ненецкий автономный округ"],
			52 => ["reg_code" => "RU_NIZ", "title" => "Нижегородская область"],
			53 => ["reg_code" => "RU_NGR", "title" => "Новгородская область"],
			54 => ["reg_code" => "RU_NVS", "title" => "Новосибирская область"],
			55 => ["reg_code" => "RU_OMS", "title" => "Омская область"],
			56 => ["reg_code" => "RU_ORE", "title" => "Оренбургская область"],
			57 => ["reg_code" => "RU_ORL", "title" => "Орловская область"],
			58 => ["reg_code" => "RU_PNZ", "title" => "Пензенская область"],
			59 => ["reg_code" => "RU_PER", "title" => "Пермский край"],
			25 => ["reg_code" => "RU_PRI", "title" => "Приморский край"],
			60 => ["reg_code" => "RU_PSK", "title" => "Псковская область"],
			61 => ["reg_code" => "RU_ROS", "title" => "Ростовская область"],
			62 => ["reg_code" => "RU_RYA", "title" => "Рязанская область"],
			63 => ["reg_code" => "RU_SAM", "title" => "Самарская область"],
			64 => ["reg_code" => "RU_SAR", "title" => "Саратовская область"],
			504 => ["reg_code" => "RU_SPE", "title" => "Санкт-Петербург"],
			65 => ["reg_code" => "RU_SAK", "title" => "Сахалинская область"],
			66 => ["reg_code" => "RU_SVE", "title" => "Свердловская область"],
			15 => ["reg_code" => "RU_SE", "title" => "Северная Осетия - Алания республика"],
			67 => ["reg_code" => "RU_SMO", "title" => "Смоленская область"],
			26 => ["reg_code" => "RU_STA", "title" => "Ставропольский край"],
			68 => ["reg_code" => "RU_TAM", "title" => "Тамбовская область"],
			16 => ["reg_code" => "RU_TA", "title" => "Татарстан республика"],
			69 => ["reg_code" => "RU_TVE", "title" => "Тверская область"],
			70 => ["reg_code" => "RU_TOM", "title" => "Томская область"],
			71 => ["reg_code" => "RU_TUL", "title" => "Тульская область"],
			72 => ["reg_code" => "RU_TYU", "title" => "Тюменская область"],
			17 => ["reg_code" => "RU_TY", "title" => "Тыва республика"],
			18 => ["reg_code" => "RU_UD", "title" => "Удмуртская республика"],
			73 => ["reg_code" => "RU_ULY", "title" => "Ульяновская область"],
			27 => ["reg_code" => "RU_KHA", "title" => "Хабаровский край"],
			19 => ["reg_code" => "RU_KK", "title" => "Хакасия республика"],
			86 => ["reg_code" => "RU_KHM", "title" => "Ханты-Мансийский автономный округ"],
			74 => ["reg_code" => "RU_CHE", "title" => "Челябинская область"],
			20 => ["reg_code" => "RU_CE", "title" => "Чеченская республика"],
			21 => ["reg_code" => "RU_CU", "title" => "Чувашская республика-ЧАВАШ РЕСПУБЛИКИ"],
			87 => ["reg_code" => "RU_CHU", "title" => "Чукотский автономный округ"],
			14 => ["reg_code" => "RU_SA", "title" => "Якутия (Саха) республика"],
			89 => ["reg_code" => "RU_YAN", "title" => "Ямало-Ненецкий автономный округ"],
			76 => ["reg_code" => "RU_YAR", "title" => "Ярославская область"]
		];

		$_areas = $this->db->query()
				->setText("SELECT sr.`id_country`, sr.`id_region`, sr.`count_firms`, sr.`count_goods`, src.`name`
		FROM `current_region_city` sr
		LEFT JOIN `sts_region_country` src ON sr.`id_country` = src.`id_country` AND sr.`id_region` = src.`id_region_country`
		WHERE sr.`id_region` IS NOT NULL AND sr.`id_country` = 643 
		ORDER BY sr.`count_firms` ASC")
				->fetch();

		$areas = [];
		foreach ($_areas as $area) {
			$areas[$area['id_region']] = $area;
		}

		$point = 10 / count($areas);
		$mark = 0;
		$prev = 0;

		foreach ($areas as $key => $area) {
			if ($prev != $areas[$key]['count_firms']) {
				$mark = $mark + $point;
			}
			$areas[$key]['mark'] = $mark;
			$prev = $areas[$key]['count_firms'];
		}

		$writer = new XMLWriter();
		$writer->openUri(AMMAP_PATH . 'ammap_data.xml');

		$writer->startDocument("1.0", "UTF-8");
		$writer->startElement($xmlmap['name']);
		foreach ($xmlmap as $key => $value) {
			$writer->writeAttribute($key, $value);
			$writer->endAttribute();
		}
		$writer->startElement("areas");
		foreach ($areacodes as $key => $areacode) {
			$writer->startElement("area");

			$writer->writeAttribute("mc_name", $areacode['reg_code']);
			$writer->endAttribute();

			$writer->writeAttribute("title", $areacode['title']);
			$writer->endAttribute();

			if (!empty($areas[$key])) {
				$writer->writeAttribute("value", isset($areas[$key]['mark']) ? $areas[$key]['mark'] : '');
			} else {
				$writer->writeAttribute("value", "");
			}
			$writer->endAttribute();

			if (isset($areas[$key]) && $areas[$key]) {
				$writer->writeElement("description", "<B>{title}</B><BR>фирм: " . number_format($areas[$key]['count_firms'], 0, '.', ' ') . "<BR>товаров&#47;услуг: " . number_format($areas[$key]['count_goods'], 0, '.', ' '));
			} else {
				$writer->writeElement("description", "<B>{title}</B><BR>нет данных");
			}

			$writer->endElement();
		}
		$writer->endElement(); //areas
		$writer->endElement(); //maps
		$writer->endDocument();
	}

	private function updateStatSite() {
		$total_towns = $this->db->query()->setText("SELECT COUNT(DISTINCT(id_city)) as `count` FROM `sts_price` WHERE `blocked` != 1")->fetch()[0]['count'];
		$total_firms = $this->db->query()->setText("SELECT COUNT(*) as `count` FROM `firm` WHERE `flag_is_active` = 1")->fetch()[0]['count'];
		$total_price = $this->db->query()->setText("SELECT COUNT(*) as `count` FROM `sts_price` WHERE `blocked` != 1")->fetch()[0]['count'];
		$total_firm_shows = $this->db->query()->setText("SELECT COUNT(*) as `count` FROM `stat_object` WHERE `model_alias` = 'firm' AND `timestamp_inserting` BETWEEN DATE_FORMAT( DATE_ADD( NOW( ) , INTERVAL -1 MONTH ) , '%Y-%m-%d 00:00:00' ) AND DATE_FORMAT( NOW( ) , '%Y-%m-%d 00:00:00' )")->fetch()[0]['count'];
		$total_price_shows = $this->db->query()->setText("SELECT COUNT(*) as `count` FROM `stat_object` WHERE `model_alias` = 'price' AND `timestamp_inserting` BETWEEN DATE_FORMAT( DATE_ADD( NOW( ) , INTERVAL -1 MONTH ) , '%Y-%m-%d 00:00:00' ) AND DATE_FORMAT( NOW( ) , '%Y-%m-%d 00:00:00' )")->fetch()[0]['count'];
		$total_visitors = $this->getTotalVisitors();

		$update_array = [
			'total_towns' => $total_towns,
			'total_firms' => $total_firms,
			'total_price' => $total_price,
			'total_firm_shows' => $total_firm_shows,
			'total_price_shows' => $total_price_shows,
			'total_visitors' => $total_visitors
		];

		$ss = new StatSite();
		$ss->get(1);

		if ($ss->exists()) {
			$ss->update($update_array);
		} else {
			$ss->insert($update_array);
		}
	}

}
