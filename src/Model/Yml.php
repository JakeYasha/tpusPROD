<?php

namespace App\Model;

class Yml extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		$c = $this->fieldPropCreator();

		return [
			'status' => $c->radioButtons_typeList('Статус', $this->getStatuses()),
			'type' => $c->radioButtons_typeList('Тип загрузки', $this->getTypes()),
			'url_firm' => $c->stringField('URL фирмы', 1024),
			'url' => $c->stringField('URL Yml-файла', 1024),
			'error_code' => $c->intField('ID ошибки', 1),
			'timestamp_yml' => $c->dateTimeField('Дата обновления файла'),
			'offers_count' => $c->intField('Количество предложений в файле', 4),
			'offers_count_loaded' => $c->intField('Количество загруженных предложений', 4),
			'data' => $c->textArea_typeBig('YML-данные'),
			'hash' => $c->stringField('Hash', 32),
			'name_format' => $c->stringField('Формат имени'),
			'flag_is_referral' => $c->checkBox('Переход по реферальной ссылке')
		];
	}

	public static function getStatuses() {
		return [
			'' => 'Подготовка к обработке...',
			'processing' => 'Обрабатывается...',
			'complete_success' => 'Успешно загружен',
			'complete_fail' => 'Ошибка загрузки',
			'deleted' => 'Удален'
		];
	}

	public static function getTypes() {
		return [
			'' => 'Загрузка из файла',
			'url' => 'Загрузка по URL'
		];
	}

	public function prepare($items) {
		$result = [];
		$statuses = $this->getStatuses();
		$types = $this->getTypes();

		foreach ($items as $item) {
			$timestamp_inserting = new \Sky4\Helper\DateTime($item->val('timestamp_inserting'));
			$timestamp_yml = new \Sky4\Helper\DateTime($item->val('timestamp_yml'));

			$result[$item->id()] = [
				'id' => $item->id(),
				'status' => $statuses[$item->val('status')],
				'type' => $types[$item->val('type')],
				'url' => $item->val('url'),
				'offers_count' => (int)$item->val('offers_count'),
				'offers_count_loaded' => (int)$item->val('offers_count_loaded'),
				'timestamp_inserting' => $timestamp_inserting->format('d.m.Y H:i:s'),
				'timestamp_yml' => $timestamp_yml->format('d.m.Y H:i:s'),
				'flag_is_referral' => (int)$item->val('flag_is_referral') === 1 ? 'да' : 'нет',
				'name_format' => $item->val('name_format')
			];
		}

		return $result;
	}

	public static function setNameFormat($format) {
		$result = [];
		if (in_array('typePrefix', $format)) {
			$result[] = 'typePrefix';
		}

		$result[] = 'name';

		if (in_array('vendor', $format)) {
			$result[] = 'vendor';
		}

		if (in_array('model', $format)) {
			$result[] = 'model';
		}

		return implode(',', $result);
	}

	//SELECT id, id_firm, status, type, url, error_code, timestamp_yml, offers_count, offers_count_loaded, hash, name_format, flag_is_referral, timestamp_inserting, timestamp_last_updating FROM `yml` ORDER BY id ASC;
}
