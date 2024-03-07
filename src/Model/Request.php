<?php

namespace App\Model;

class Request extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\CompanyDataTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function cols() {
		$cols = [
			'user_name' => [
				'label' => 'ФИО'
			],
			'company_name' => [
				'label' => 'Название компании'
			],
			'town' => [
				'label' => 'Город'
			]
		];

		$cols = array_merge($cols, $this->timestampActionComponent()->cols('timestamp_inserting'));

		return $cols;
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'CompanyData'],
			//['type' => 'field', 'name' => 'company_web_site_url'],
			['type' => 'field', 'name' => 'town'],
			['type' => 'label', 'text' => 'Представитель'],
			['type' => 'field', 'name' => 'user_name'],
			['type' => 'field', 'name' => 'appointment'],
			['type' => 'tab', 'name' => 'stab', 'label' => 'Услуги и файлы'],
			['type' => 'field', 'name' => 'services', 'tab_name' => 'stab'],
			['type' => 'field', 'name' => 'files', 'tab_name' => 'stab'],
		];
	}

	public function defaultOrder() {
		return [
			'timestamp_inserting' => 'DESC'
		];
	}

	public function defaultInsertingEnabled() {
		return false;
	}

	public function fields() {
		return [
			'appointment' => [//ex post
				'elem' => 'text_field',
				'label' => 'Должность',
				'params' => [
					'rules' => ['length' => ['max' => 255, 'min' => 2], 'required']
				]
			],
			'town' => [
				'elem' => 'text_field',
				'attrs' => [
					'class' => 'js-autocomplete',
					'id' => 'town-autocomplete',
					'placeholder' => 'Введите название...',
					'data-name' => 'id_region_country',
					'data-settings' => '',
					'data-container' => 'request',
					'data-val-mode' => 'id',
					'data-model-alias' => 'sts-city',
					'data-field-name' => 'name'
				],
				'label' => 'Город',
				'params' => [
					'rules' => ['length' => ['max' => 255, 'min' => 2], 'required']
				]
			],
			'id_region_country' => [
				'elem' => 'hidden_field',
				'label' => ''
			],
			'flag_is_new' => [
				'elem' => 'single_check_box',
				'label' => 'Новое?'
			],
			'services' => [
				'col' => [
					'flags' => '',
					'type' => 'text_2'
				],
				//'elem' => 'custom_check_boxes', @todo
				'elem' => 'check_boxes',
				'label' => 'Перечень услуг информационного центра со ссылками на описание услуги и возможностью выбора пользователем этой услуги',
				'options' => \Sky4\Container::getList('Service', 'getListForCheckboxes')
			],
			'files' => [
				'elem' => 'html_elem',
				'label' => 'Загруженные файлы'
			],
		];
	}

	public function title() {
		return 'Заявки';
	}

	public function name() {
		return $this->exists() ? $this->val('company_name') : 'Заявки';
	}

}
