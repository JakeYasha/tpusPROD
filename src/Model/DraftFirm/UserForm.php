<?php

namespace App\Model\DraftFirm;

use App\Model\DraftFirm;
use App\Model\Firm;
use Sky4\Model;
use Sky4\Model\Form;
use Sky4\Widget\InterfaceElem\Creator;
use function app;
use function str;

class UserForm extends Form {

	private $firm;

	public function __construct(Model $model = null, $firm = null) {
		if (!($this->model() instanceof DraftFirm)) {
			$this->setModel(new DraftFirm());
		}
		$this->firm = $firm;
		parent::__construct($model, []);
	}

	public function editableFieldsNames() {
		return array_keys($this->model()->companyData()->fields() + $this->model()->fields() + $this->model()->idFirmComponent()->fields());
	}

	public function setFirm(Firm $firm) {
		$this->firm = $firm;
		return $this;
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Сохранить',
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
			'action' => '/firm-user/submit/draft-firm/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fields = $this->model()->companyData()->fields() + $this->model()->fields() + $this->model()->idFirmComponent()->fields() + $this->model()->textComponent()->fields();
		$result = [];
		$firm = $this->firm;
		$geo = $firm != null ? $this->getGeoData($firm) : ['region_code' => '', 'country_code' => '', 'city_name' => ''];
		//
		$result['id_firm'] = $fields['id_firm'];
		$result['id_firm']['elem'] = 'hidden_field';
		//
		$result['company_name'] = $fields['company_name'];
		$result['company_email'] = $fields['company_email'];
		$result['company_phone'] = $fields['company_phone'];
		$result['company_phone']['label'] .= ' <span class="hint">+'.$geo['country_code'].' ('.$geo['region_code'].')'.'</span>';
		$result['company_fax'] = $fields['company_fax'];
		$result['company_fax']['label'] .= ' <span class="hint">+'.$geo['country_code'].' ('.$geo['region_code'].')'.'</span>';
		$result['company_address'] = $fields['company_address'];
		$result['company_address']['label'] .= ' <span class="hint">'.$geo['city_name'].'</span>';
		$result['path'] = $fields['path'];
		$result['text'] = $fields['text'];
		$result['text']['label'] = 'Дополнительная информация (адреса и контакты филиалов, лицензии, сертификаты и пр.)';
		$result['text']['attrs']['class'] = 'js-autosize';
		//$result['company_about'] = $fields['company_about'];
		$result['company_cell_phone'] = $fields['company_cell_phone'];
		$result['company_web_site_url'] = $fields['company_web_site_url'];
		$result['company_activity'] = $fields['company_activity'];
		$result['mode_work'] = $fields['mode_work'];
		$result['company_activity']['elem'] = 'text_area';
		$result['text']['elem'] = 'text_area';

		//rules
		$result['path']['params']['rules'] = ['length' => ['max' => 500]];
		$result['mode_work']['params']['rules'] = ['length' => ['max' => 500]];
		$result['company_activity']['params']['rules'] = ['length' => ['max' => 255]];

		if ($this->model()->exists()) {
			$draft_vals = $this->model()->getVals();
			$firm_vals = $firm->getVals();

			foreach ($result as $k => $v) {
				if ($firm_vals[$k] !== $draft_vals[$k]) {
					$result[$k]['attrs']['style'] = 'background-color:#ffffc1';
				}
			}
		}




		//$result['company_phone']['label'] = $this->model
//		$result['file_logo'] = $fields['file_logo'];
//		$result['file_logo']['elem'] = 'hidden_field';
//		$result['file_logo']['attrs']['class'] = 'js-upload-attach-id';
		//$result['company_activity']['elem'] = 'tiny_mce';

		return $result;
	}

	private function getGeoData(Firm $firm) {
		$geo_data = $firm->geoDataComponent()->getGeoData();
		$city_name = '';
		if ($geo_data['cityType'] == 19) {
			$city_name .= 'г. ';
		} else {
			$cityType = app()->db()->query()
					->setSelect(['name'])
					->setFrom(['sts_city_type'])
					->setWhere('`id_city_type` = :city_type', [':city_type' => $geo_data['cityType']])
					->selectRow();

			$city_name .= trim(str()->firstCharToUpper(str()->toLower($cityType['name']))).' ';
		}

		$city_name .= trim(str()->firstCharsOfWordsToUpper(str()->toLower($geo_data['city'])));
		$region_code = $geo_data['cityCode'];
		$country_code = $geo_data['countryCode'];

		// КОСТЫЛЬ ДЛЯ КОДА СТРАНЫ
		if ($geo_data['id_country'] == '643') {
			$country_code = '7';
		}

		return ['region_code' => $region_code, 'country_code' => $country_code, 'city_name' => $city_name];
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', 'Общая информация о фирме')
						->set('sub_heading', 'Здесь вы можете изменить информацию о фирме')
						->setTemplate('info_form_user', 'forms')
						->render();
	}

	public function renderFields() {
		$result = [];
		$elem_creator = new Creator();
		foreach ($this->getFields() as $field_name => $field_props) {
			if (in_array($field_name, ['text', 'company_activity', 'path', 'mode_work'])) {
				$field_props['val'] = str()->replace($field_props['val'], '\\', PHP_EOL);
			}
			$field_name = (string)$field_name;
			$field_props = (array)$field_props;
			$_field_props = $field_props;
			$_field_props['elem_mode'] = 'val';
			$result[$field_name] = [
				'elem' => (string)$field_props['elem'],
				'html' => $elem_creator->renderElem($field_name, $field_props),
				'label' => (isset($field_props['label']) && $field_props['label']) ? (string)$field_props['label'] : '-',
				'val' => $elem_creator->renderElem($field_name, $_field_props)
			];
		}
		return $result;
	}

}
