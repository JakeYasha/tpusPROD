<?php

namespace App\Model\FirmPromo;

use App\Model\Firm;
use App\Model\FirmPromo;
use App\Model\PriceCatalog;
use App\Model\Price;
use CDate;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;

class UserForm extends \Sky4\Model\Form {

	public function __construct(\Sky4\Model $model = null, $params = null) {
		if (!($this->model() instanceof FirmPromo)) {
			$this->setModel(new FirmPromo());
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
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/promo/?success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fields = $this->model()->getFields();
		$fields['name']['label'] = 'Название акции';
		$fields['flag_is_active']['label'] = 'Показывать на сайте';
		$fields['flag_is_active']['default_val'] = 0;
		$fields['catalog_ids']['elem'] = 'hidden_field';
		$fields['catalog_ids']['attrs']['class'] = 'js-rubric-selector-holder';
		$fields['timestamp_beginning']['elem'] = 'app/classes/dmy_date';
		$fields['timestamp_beginning']['default_val'] = CDate::fromTimestamp(DeprecatedDateTime::toTimestamp(DeprecatedDateTime::now()));
		$fields['timestamp_ending']['elem'] = 'app/classes/dmy_date';
		$fields['timestamp_ending']['default_val'] = CDate::fromTimestamp(DeprecatedDateTime::toTimestamp(DeprecatedDateTime::shiftMonths(+1)));
		$fields['flag_is_infinite']['attrs']['class'] = 'js-set-infinite-date';
		return [
			'id' => ['elem' => 'hidden_field'],
			'id_firm' => ['elem' => 'hidden_field'],
			'id_city' => ['elem' => 'hidden_field'],
			'image' => ['elem' => 'hidden_field', 'attrs' => ['class' => 'js-upload-id-holder']],
			'catalog_ids' => $fields['catalog_ids'],
			//
			'name' => $fields['name'],
			'phone' => $fields['phone'],
			'flag_is_present' => $fields['flag_is_present'],
			'percent_value' => $fields['percent_value'],
			'timestamp_beginning' => $fields['timestamp_beginning'],
			'timestamp_ending' => $fields['timestamp_ending'],
			'flag_is_infinite' => $fields['flag_is_infinite'],
			'brief_text' => $fields['brief_text'],
			'text' => $fields['text'],
			'flag_is_active' => $fields['flag_is_active'],
		];
	}

	// -------------------------------------------------------------------------

	public function render() {
		if ($this->model()->exists()) {
			$images = Utils::getObjectsByIds($this->model()->val('image'));
			$edit_row = $this->model()->prepare($this->model(), isset($images[$this->model()->val('image')]) ? $images[$this->model()->val('image')]->iconLink('-320x180') : null);
			$image_url = $edit_row['image'] ? $edit_row['image'] : '/img/no_img.png';
		} else {
			$edit_row = [];
			$image_url = '/img/no_img.png';
		}

		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						//
						->set('active_rubrics', $this->model()->exists() ? explode(',', $this->model()->val('catalog_ids')) : [])
						->set('rubrics', $this->getSubgroups())
						->set('image_url', $image_url)
						->setTemplate('firm_promo_form_user', 'forms')
						->render();
	}

	private function getSubgroups() {
		$firm = new Firm($this->getVal('id_firm'));


		$sp = new Price();
		$subgroup_ids = $sp->reader()
				->setSelect('DISTINCT id_subgroup')
				->setWhere(['AND', 'id_firm = :id_firm', 'flag_is_active = :flag_is_active'], [':id_firm' => $firm->id(), ':flag_is_active' => 1])
				->rowsWithKey('id_subgroup');

		if ($this->model()->exists()) {
			$model_rubrics = explode(',', $this->model()->val('catalog_ids'));
			foreach ($model_rubrics as $rub) {
				if (!isset($subgroup_ids[$rub])) {
					$subgroup_ids[$rub] = 1;
				}
			}
		}

		$groups = [];
		if ($subgroup_ids) {
			$pc = new PriceCatalog();
			$id_subgroup_conds = Utils::prepareWhereCondsFromArray(array_keys($subgroup_ids), 'id_subgroup');
			$where = ['AND', $id_subgroup_conds['where'], 'node_level = :node_level'];
			$params = $id_subgroup_conds['params'] + [':node_level' => 2];
			$groups = $pc->reader()
					->setSelect(['id', 'web_many_name as name'])
					->setWhere($where, $params)
					->setOrderBy('web_many_name ASC')
					->rowsWithKey('id');
		}

		return $groups;
	}

}
