<?php

namespace App\Model\AdvertModule;

use App\Model\AdvertModule;
use App\Model\FirmType;
use App\Model\PriceCatalog;
use CDate;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model;
use Sky4\Model\Utils;

class UserForm extends \Sky4\Model\Form {

	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof AdvertModule)) {
			$this->setModel(new AdvertModule());
		}
		parent::__construct($model, $params);
	}

	public function editableFieldsNames() {
		return array_keys($this->fields());
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => $this->model()->exists() ? 'Сохранить' : 'Добавить',
				'attrs' => [
					'class' => 'send js-send btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/advert-module/?success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fields = $this->model()->getFields();
		$fields['name']['label'] = 'Название рекламного модуля (для отображения в личном кабинете)';
		$fields['name']['attrs']['class'] = 'form__control form__control_modal';
		$fields['email']['params']['rules'] = ['length' => ['max' => 50], 'email'];
		$fields['email']['attrs']['class'] = 'form__control form__control_modal';
		$fields['phone']['params']['rules'] = ['length' => ['max' => 18, 'min' => 6]];
		$fields['phone']['attrs'] = ['class' => 'js-masked-phone form__control form__control_modal'];
		//$fields['header']['label'] = 'Заголовок рекламного модуля (100 символов)';
		$fields['flag_is_active']['label'] = 'Показывать на сайте';
		$fields['flag_is_active']['default_val'] = 0;
		$fields['flag_is_infinite']['label'] = 'Бессрочный';
		$fields['flag_is_infinite']['default_val'] = 0;
		$fields['region_ids']['elem'] = 'hidden_field';
		//$fields['subgroup_ids']['elem'] = 'hidden_field';
		//$fields['subgroup_ids']['attrs']['class'] = 'js-rubric-selector-holder';
		$fields['subgroup_ids']['label'] = 'Подгруппы соответствующие тематике модуля';
		$fields['subgroup_ids']['attrs']['data-prefill'] = json_encode(array_values($this->getSubgroups()));
		//$fields['firm_type_ids']['elem'] = 'hidden_field';
		//$fields['firm_type_ids']['attrs']['class'] = 'js-firm-type-selector-holder';
		$fields['firm_type_ids']['label'] = 'Закрепите несколько рубрик каталога фирм';
		$fields['firm_type_ids']['attrs']['data-prefill'] = json_encode(array_values($this->getFirmTypes()));
		$fields['timestamp_beginning']['elem'] = 'app/classes/dmy_date';
		$fields['timestamp_beginning']['default_val'] = CDate::fromTimestamp(DeprecatedDateTime::toTimestamp(DeprecatedDateTime::now()));
		$fields['timestamp_ending']['elem'] = 'app/classes/dmy_date';
		$fields['timestamp_ending']['default_val'] = CDate::fromTimestamp(DeprecatedDateTime::toTimestamp(DeprecatedDateTime::shiftMonths(+1)));
		$fields['flag_is_infinite']['attrs']['class'] = 'js-set-infinite-date';
		return [
			'id' => ['elem' => 'hidden_field'],
			'id_firm' => ['elem' => 'hidden_field'],
			//'id_service' => ['elem' => 'hidden_field'],
			'id_city' => ['elem' => 'hidden_field'],
			'target_btn_name' => ['elem' => 'hidden_field'],
			'callback_btn_name' => ['elem' => 'hidden_field'],
			'image' => ['elem' => 'hidden_field', 'attrs' => ['class' => 'js-upload-id-holder advert-module-image']],
			'full_image' => ['elem' => 'hidden_field', 'attrs' => ['class' => 'js-upload-id-holder advert-module-full-image']],
			'subgroup_ids' => $fields['subgroup_ids'],
			'firm_type_ids' => $fields['firm_type_ids'],
			'region_ids' => $fields['region_ids'],
			//
			//'type' => ['elem' => 'hidden_field', 'attrs' => ['disabled' => 'disabled']], // заглушка, чтобы получить данные из select
			'name' => ['elem' => 'hidden_field'],
			'header' => $fields['header'],
			'adv_text' => $fields['adv_text'],
			//'flag_is_everywhere' => $fields['flag_is_everywhere'],
			//'flag_is_commercial' => $fields['flag_is_commercial'],
			'url' => $fields['url'],
			'more_url' => $fields['more_url'],
			'email' => $fields['email'],
			'phone' => $fields['phone'],
			'about_string' => $fields['about_string'],
			'phones' => $fields['phones'],
			'timestamp_beginning' => $fields['timestamp_beginning'],
			'timestamp_ending' => $fields['timestamp_ending'],
			'flag_is_infinite' => $fields['flag_is_infinite'],
			'flag_is_active' => $fields['flag_is_active']
		];
	}

	// -------------------------------------------------------------------------

	public function render() {
		if ($this->model()->exists()) {
			$images = Utils::getObjectsByIds($this->model()->val('image'));
			$full_images = Utils::getObjectsByIds($this->model()->val('full_image'));
			$edit_row = $this->model()->prepare($this->model(), isset($images[$this->model()->val('image')]) ? $images[$this->model()->val('image')]->iconLink('-thumb') : null, isset($full_images[$this->model()->val('full_image')]) ? $full_images[$this->model()->val('full_image')]->iconLink('-thumb') : null);
			$image_url = $edit_row['image'] ? $edit_row['image'] : '/img/no_img.png';
			$full_image_url = $edit_row['full_image'] ? $edit_row['full_image'] : '/img/no_img.png';
		} else {
			$edit_row = [];
			$image_url = '/img/no_img.png';
			$full_image_url = '/img/no_img.png';
		}

		return $this->view()
						->set('item', $this->model())
						->set('attrs', $this->getAttrs())
						->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('types', $this->model()->types())
						->set('type', $this->model()->exists() ? $this->model()->val('type') : '')
						->set('target_btn_names', $this->model()->target_btn_names())
						->set('target_btn_name', $this->model()->exists() ? $this->model()->val('target_btn_name') : 'onlineshop')
						->set('callback_btn_names', $this->model()->callback_btn_names())
						->set('callback_btn_name', $this->model()->exists() ? $this->model()->val('callback_btn_name') : 'join')
						//
						->set('active_subgroups', $this->model()->exists() ? explode(',', $this->model()->val('subgroup_ids')) : [])
						->set('active_firm_types', $this->model()->exists() ? explode(',', $this->model()->val('firm_type_ids')) : [])
						->set('firm_types', $this->getFirmTypes())
						->set('image_url', $image_url)
						->set('full_image_url', $full_image_url)
						->setTemplate('advert_module_form_user', 'forms')
						->render();
	}

	private function getSubgroups() {
		$subgroup_ids = [];

		if ($this->model()->exists()) {
			$subgroup_ids = array_filter(explode(',', $this->model()->val('subgroup_ids')));
		}
		$groups = [];
		if ($subgroup_ids) {
			$pc = new PriceCatalog();
			$id_subgroup_conds = Utils::prepareWhereCondsFromArray($subgroup_ids, 'id_subgroup');

			$where = ['AND', $id_subgroup_conds['where'], 'node_level = :node_level'];
			$params = $id_subgroup_conds['params'] + [':node_level' => 2];
			$groups = $pc->reader()
					->setSelect(['id_subgroup as id', 'web_many_name as name'])
					->setWhere($where, $params)
					->setOrderBy('web_many_name ASC')
					->rows();
		}

		return $groups;
	}

	private function getFirmTypes() {
		$firm_type_ids = [];
		if ($this->model()->exists()) {
			$firm_type_ids = array_filter(explode(',', $this->model()->val('firm_type_ids')));
		}

		$firm_types = [];
		if ($firm_type_ids) {
			$firm_type_conds = Utils::prepareWhereCondsFromArray($firm_type_ids, 'id');
			$ft = new FirmType();

			$firm_types = $ft->reader()
					->setSelect(['id', 'name'])
					->setWhere($firm_type_conds['where'], $firm_type_conds['params'])
					->setOrderBy('`count` DESC')
					->rows();
		}


		return $firm_types;
	}

}
