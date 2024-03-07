<?php

namespace App\Model\FirmBranch;

use App\Model\Firm;
use App\Model\FirmBranch;
use Sky4\Model;
use Sky4\Model\Form;
use Sky4\Widget\InterfaceElem\Creator;

class FormAdd extends Form {

    private $firm;

    public function __construct(Model $model = null, $firm = null) {
        if (!($this->model() instanceof FirmBranch)) {
            $this->setModel(new FirmBranch());
        }
        $this->firm = $firm;
        parent::__construct($model, []);
    }

    public function controls() {
        return [
            'submit' => [
                'elem' => 'button',
                'label' => 'Сохранить',
                'attrs' => [
                    'class' => 'send js-send btn btn_primary'
                ]
            ]
        ];
    }

    public function attrs() {
        return [
            'accept-charset' => 'utf-8',
            'action' => '/firm-user/firm-branch/submit/',
            'method' => 'post'
        ];
    }
    
	public function fields() {
        $fields = $this->model()->getFields();
        $result = [];
        $_firm_branch = new FirmBranch();
        $firm = $this->firm;
        if ($firm != null) {
            $_firm_branch->setVals($firm->getVals());
        }
		$geo = $firm != null ? $this->getGeoData($_firm_branch) : ['region_code' => '', 'country_code' => '', 'city_name' => ''];
		//
		$result['id_firm'] = $fields['id_firm'];
		$result['id_firm']['elem'] = 'hidden_field';
		$result['id_service'] = $fields['id_service'];
		$result['id_service']['elem'] = 'hidden_field';
        
        $result['firm_id'] = $fields['firm_id'];
		$result['firm_id']['elem'] = 'hidden_field';
		//
        $result['town']['elem'] = 'text_field';
        $result['town']['attrs'] = [
            'class' => 'js-autocomplete',
            'id' => 'town-autocomplete',
            'placeholder' => 'Введите название...',
            'data-name' => 'id_city',
            'data-settings' => '',
            'data-container' => 'firm-branch',
            'data-val-mode' => 'id_city',
            'data-model-alias' => 'sts-city',
            'data-field-name' => 'name'            
        ];
        $result['town']['label'] = 'Город. <span class="red">* - если города нет в списке - обратитесь К Вашему менеджеру.</span>';
        $result['town']['params'] = [
            'rules' => ['length' => ['max' => 255, 'min' => 2], 'required']
        ];

        $result['id_city'] = $fields['id_city'];
		$result['id_city']['elem'] = 'hidden_field';
        
        $result['id_country'] = $fields['id_country'];
		$result['id_country']['elem'] = 'hidden_field';

        $result['id_region_country'] = $fields['id_region_country'];
		$result['id_region_country']['elem'] = 'hidden_field';

        $result['id_region_city'] = $fields['id_region_city'];
		$result['id_region_city']['elem'] = 'hidden_field';
        //
		$result['company_name'] = $fields['company_name'];
		$result['company_name_jure'] = $fields['company_name_jure'];
		$result['company_email'] = $fields['company_email'];
		$result['company_phone'] = $fields['company_phone'];
		$result['company_phone']['label'] .= ' <span class="hint">+'.$geo['country_code'].' ('.$geo['region_code'].')'.'</span>';
		$result['company_fax'] = $fields['company_fax'];
		$result['company_fax']['label'] .= ' <span class="hint">+'.$geo['country_code'].' ('.$geo['region_code'].')'.'</span>';
		$result['company_address'] = $fields['company_address'];
		$result['company_address']['label'] .= ' <span class="hint">'.$geo['city_name'].'</span>';
		/*$result['company_map_address'] = $fields['company_address'];
		$result['company_map_address']['label'] .= ' <span class="hint">'.$geo['city_name'].'</span>';*/
		$result['path'] = $fields['path'];
		$result['text'] = $fields['text'];
		$result['text']['label'] = 'Дополнительная информация (лицензии, сертификаты и пр.)';
		$result['text']['attrs']['class'] = 'js-autosize';
		//$result['company_about'] = $fields['company_about'];
		$result['company_web_site_url'] = $fields['company_web_site_url'];
		$result['company_activity'] = $fields['company_activity'];
		$result['mode_work'] = $fields['mode_work'];
		$result['company_activity']['elem'] = 'text_area';
		$result['text']['elem'] = 'text_area';

		//rules
		$result['path']['params']['rules'] = ['length' => ['max' => 500]];
		$result['mode_work']['params']['rules'] = ['length' => ['max' => 500]];
		$result['company_activity']['params']['rules'] = ['length' => ['max' => 255]];

		$result['company_vk'] = $fields['company_vk'];
		$result['company_fb'] = $fields['company_fb'];
		$result['company_in'] = $fields['company_in'];
		$result['company_viber'] = $fields['company_viber'];
		$result['company_whatsapp'] = $fields['company_whatsapp'];
		$result['company_skype'] = $fields['company_skype'];
		$result['company_telegram'] = $fields['company_telegram'];

        $result['flag_is_price_attached']['elem'] = 'single_check_box';
        $result['flag_is_price_attached']['label'] = 'Привязать прайс основной фирмы';
        
		return $result;
	}
    
    public function render() {
		return $this->view()->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', 'Информация о филиале')
						->set('sub_heading', 'Здесь вы можете изменить информацию о филиале')
						->setTemplate('branch_form_user', 'forms')
						->render();
	}
    
    public function renderFields() {
		$result = [];
		$elem_creator = new Creator();
		foreach ($this->getFields() as $field_name => $field_props) {
			if (in_array($field_name, ['text', 'company_activity', 'path', 'mode_work'])) {
				$field_props['val'] = str()->replace($field_props['val'], '\\', PHP_EOL);
			}
            
            if ($field_name == 'town') {
                if ($this->model()->exists() && $this->model()->val('id_city')) {
                    $_city = new \App\Model\StsCity($this->model()->val('id_city'));
                    $field_props['val'] = $_city->name();
                }
            }
            if ($field_name == 'firm_id') {
                $field_props['val'] = $this->firm->id();
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

	private function getGeoData(FirmBranch $firm_branch) {
		$geo_data = $firm_branch->geoDataComponent()->getGeoData();
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

}
