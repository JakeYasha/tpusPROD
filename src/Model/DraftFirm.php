<?php

namespace App\Model;

class DraftFirm extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\CompanyDataTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait;

	public function afterInsert(&$vals, $parent_object = null) {
		$connected_firm = new Firm();
		$connected_firm->getByIdFirm($this->id_firm());

		$changed_data = $this->getChangedFirmData();
		$manager = new FirmManager();
		$manager->getByFirm($connected_firm);
		$email = $manager->val('email') ? $manager->val('email') : $manager->val('email_default'); //app()->firmUser()->val('email');
		// Если id_service == 10 - отправляем письмо специалисту, иначе - отправляем на email службы
		if ($this->id_service() !== 10) {
			$service = new StsService();
			$service->reader()
					->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $this->id_service()])
					->objectByConds();

			$email = $service->val('email');
		}

		app()->email()
				->setSubject('Информация о фирме изменена')
				->setTo($email)
				->setParams(['changed_data' => $changed_data])
				->setModel($connected_firm)
				->setTemplate('email_to_manager', 'request')
				->sendToQuery();

		return parent::afterInsert($vals, $parent_object);
	}

	public function afterUpdate(&$vals) {
		$connected_firm = new Firm();
		$connected_firm->getByIdFirm($this->id_firm());

		$changed_data = $this->getChangedFirmData();
		$manager = new FirmManager();
		$manager->getByFirm($connected_firm);
		$email = $manager->val('email') ? $manager->val('email') : $manager->val('email_default'); //app()->firmUser()->val('email');
		// Если id_service == 10 - отправляем письмо специалисту, иначе - отправляем на email службы
		if ($this->id_service() !== 10) {
			$service = new StsService();
			$service->reader()
					->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $this->id_service()])
					->objectByConds();
			$email = $service->val('email');
		}

		app()->email()
				->setSubject('Информация о фирме изменена')
				->setTo($email)
				->setModel($connected_firm)
				->setParams(['changed_data' => $changed_data])
				->setTemplate('email_to_manager', 'request')
				->sendToQuery();

		return parent::afterUpdate($vals);
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		foreach ($vals as $field_name => $val) {
			if (in_array($field_name, ['text', 'company_activity', 'path', 'mode_work'])) {
				$vals[$field_name] = str()->replace($val, PHP_EOL, '\\');
			}
		}
		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		foreach ($vals as $field_name => $val) {
			if (in_array($field_name, ['text', 'company_activity', 'path', 'mode_work'])) {
				$vals[$field_name] = str()->replace($val, PHP_EOL, '\\');
			}
		}
		return parent::beforeUpdate($vals);
	}

	public function getChangedFirmData() {
		$waste_vals = ['id_firm', 'company_name_ratiss', 'company_name_jure', 'company_map_address', 'company_phone_readdress', 'timestamp_inserting', 'timestamp_last_updating', 'file_logo'];
		$connected_firm = new Firm();
		$connected_firm->getByIdFirm($this->id_firm());
		$draft_vals = $this->getVals();
		$base_vals = $connected_firm->getVals();

		$changedData = [];
		$fields = $this->companyDataComponent()->fields() + $this->fields() + $this->idFirmComponent()->fields() + $this->textComponent()->fields();

		foreach ($base_vals as $k => $v) {
			if (isset($draft_vals[$k]) && self::normalizeFirmField($draft_vals[$k]) !== self::normalizeFirmField($v) && !in_array($k, $waste_vals) && $k !== 'id') {
				$field_name = isset($fields[$k]['label']) ? $fields[$k] : ['label' => $k];
				$changedData[] = ['name' => $field_name, 'new_value' => $draft_vals[$k], 'old_value' => $v];
			}
		}

		return $changedData;
	}

	public static function normalizeFirmField($string) {
		return preg_replace('~[^a-zA-Zа-яА-Я0-9]~u', '', $string);
	}

	public function cols() {
		return [
			'id_firm' => ['label' => 'Фирма'],
			'timestamp_inserting' => ['label' => 'Дата изменения', 'style_class' => 'date-time'],
		];
	}

	public function fields() {
		return [
			'company_name_ratiss' => [
				'elem' => 'text_field',
				'label' => 'Имя Ратисс',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
			'company_name_jure' => [
				'elem' => 'text_field',
				'label' => 'Юридическое название',
				'params' => [
					'rules' => ['length' => array('max' => 200, 'min' => 1)]
				]
			],
			'company_cell_phone' => [
				'elem' => 'text_field',
				'label' => 'Сотовый телефон для сообщений',
				'params' => [
					'rules' => ['length' => array('max' => 30, 'min' => 1)]
				]
			],
			'company_map_address' => [
				'elem' => 'text_field',
				'label' => 'Адрес на карте',
				'params' => [
					'rules' => ['length' => array('max' => 300, 'min' => 1)]
				]
			],
			'mode_work' => [
				'elem' => 'text_field',
				'label' => 'Режим работы',
				'params' => [
					'rules' => ['length' => array('max' => 500, 'min' => 1)]
				]
			],
			'company_phone_readdress' => [
				'elem' => 'text_field',
				'label' => 'Телефон переадресации',
				'params' => [
					'rules' => ['length' => array('max' => 64, 'min' => 1)]
				]
			],
			'company_fax' => [
				'elem' => 'text_field',
				'label' => 'Факс',
				'params' => [
					'rules' => ['length' => array('max' => 64, 'min' => 1)]
				]
			],
			'file_logo' => [
				'elem' => 'text_field',
				'label' => 'Путь до логотипа',
				'params' => [
					'rules' => ['length' => array('max' => 100, 'min' => 1)]
				]
			],
			'path' => [
				'elem' => 'text_field',
				'label' => 'Как проехать',
				'params' => [
					'rules' => ['length' => array('max' => 500, 'min' => 1)]
				]
			],
			'text' => [
				'attrs' => ['rows' => '10'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Текст',
				'params' => [
					'parser' => true
				]
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
				'field_name' => 'id_firm',
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'id_firm']
		];
	}

	public function formStructure() {
		$bad_vals = ['company_name_ratiss', 'company_name_jure', 'company_map_address', 'company_phone_readdress'];
		$connected_firm = new Firm();
		$connected_firm->getByIdFirm($this->id_firm());
		$draft_vals = $this->getVals();
		$base_vals = $connected_firm->getVals();

		$structure = [];
		foreach ($base_vals as $k => $v) {
			if (isset($draft_vals[$k]) && trim($draft_vals[$k]) !== trim($v) && !in_array($k, $bad_vals) && $k !== 'id') {
				$structure[] = ['type' => 'field', 'name' => $k];
			}
		}

		return $structure;
	}

	public function title() {
		return $this->exists() ? 'Модерация изменений <strong>' . $this->val('company_name') . '</strong> [' . $this->id_firm() . '/' . $this->id_service() . '] от ' . date('d.m.Y H:i:s', \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_inserting'))) : 'Модерация изменений';
	}

	public function defaultOrder() {
		return ['timestamp_inserting' => 'desc'];
	}

	public function orderableFieldsNames() {
		return ['timestamp_inserting'];
	}

}
