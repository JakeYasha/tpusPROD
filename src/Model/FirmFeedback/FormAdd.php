<?php

namespace App\Model\FirmFeedback;

use App\Model\FeedbackOptions;
use App\Model\Firm;
use App\Model\FirmFeedback;
use CInterfaceElemCreator;

class FormAdd extends \Sky4\Model\Form {

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить',
				'attrs' => [
					'class' => 'send js-ajax-send btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-feedback/submit/',
			'enctype' => 'application/x-www-form-urlencoded',
			'method' => 'post'
		];
	}

	public function fields() {
		$model = new FirmFeedback();
		$result = [];
		$fields = $model->getFields();
        
		$fields['user_name']['params']['rules'] = ['required'];
        $fields['user_name']['attrs']['class'] = 'form__control form__control_modal';

		$fields['user_email']['params']['rules'] = ['required', 'email'];
        $fields['user_email']['attrs']['class'] = 'form__control form__control_modal';

		$fields['message_subject']['label'] = 'Тема сообщения';
		$fields['message_subject']['params']['rules'] = ['required'];
        $fields['message_subject']['attrs']['class'] = 'form__control form__control_modal';

		$fields['message_text']['label'] = 'Ваше сообщение (запрос, предложение, обращение)';
		$fields['message_text']['params']['rules'] = ['required'];
		$fields['message_text']['elem'] = 'text_area';
		$fields['message_text']['attrs']['style'] = 'height: 150px';
        $fields['message_text']['attrs']['class'] = 'form__control form__control_modal';

		$result['user_name'] = $fields['user_name'];
		$result['user_email'] = $fields['user_email'];
		$result['message_subject'] = $fields['message_subject'];
		$result['message_text'] = $fields['message_text'];

		return $result;
	}

	public function errorFields() {
		$model = new FirmFeedback();
		$result = [];
		$fields = $model->getFields();

		$fields['user_name']['label'] = 'Ваше имя или название фирмы';
		$fields['user_name']['params']['rules'] = ['required'];
		$fields['user_name']['attrs']['class'] = 'form__control form__control_modal';
        
        $fields['user_email']['label'] = 'E-mail для обратной связи';
		$fields['user_email']['params']['rules'] = ['required', 'email'];
        $fields['user_email']['attrs']['class'] = 'form__control form__control_modal';

		$fields['message_subject']['label'] = 'Тема сообщения';
		$fields['message_subject']['params']['rules'] = ['required'];
        $fields['message_subject']['attrs']['class'] = 'form__control form__control_modal';

		$fields['message_text']['label'] = 'Ваше сообщение об ошибке';
		$fields['message_text']['params']['rules'] = ['required'];
		$fields['message_text']['elem'] = 'text_area';
		$fields['message_text']['attrs']['style'] = 'height: 150px';
        $fields['message_text']['attrs']['class'] = 'form__control form__control_modal';

		$result['user_name'] = $fields['user_name'];
		$result['user_email'] = $fields['user_email'];
		$result['message_subject'] = $fields['message_subject'];
		$result['message_text'] = $fields['message_text'];

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render(Firm $firm, $heading = 'Написать письмо', $id_feedback_option = null, $old_form = false) {
		$fo = new FeedbackOptions();
		if ($id_feedback_option !== null) {
			$fo->get($id_feedback_option);
			if ($fo->exists()) {
				$heading = $fo->name();
			}
		}

		$view = $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields($fo))
						->set('heading', $heading)
						->set('firm', $firm)
						->set('sub_heading', '')
						->set('feedback_option', $fo);

        return $view->setTemplate('firm_feedback_add_form', 'forms')
						->render();
	}

	public function renderFields(FeedbackOptions $feedback_option = null) {
		$result = array();
		$elem_creator = new CInterfaceElemCreator();
		foreach ($this->getFields() as $field_name => $field_props) {
			$field_name = (string) $field_name;
			$field_props = (array) $field_props;

			if ($field_name === 'message_subject' && $feedback_option->exists()) {
				$field_props['default_val'] = $feedback_option->val('subject');
			}

			$result[$field_name] = array(
				'elem' => (string) $field_props['elem'],
				'html' => $elem_creator->renderElem($field_name, $field_props),
				'label' => (isset($field_props['label']) && $field_props['label']) ? (string) $field_props['label'] : '-'
			);
		}
		return $result;
	}

	public function renderErrorForm($heading = 'Сообщить об ошибке', $id_firm, $id_service = null) {
		$firm = new Firm();
		if ($id_service === null) {
			$firm->getByIdFirm($id_firm);
		} else {
			$firm->getByIdFirmAndIdService($id_firm, $id_service);
		}

		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderErrorFields())
						->set('heading', $heading)
						->set('firm', $firm)
						->set('sub_heading', '')
						->setTemplate('firm_feedback_add_form_error', 'forms')
						->render();
	}

	public function renderErrorFields() {
		$result = array();
		$elem_creator = new CInterfaceElemCreator();
		$fields = $this->errorFields();
		foreach ($fields as $field_name => $field_props) {
			$field_name = (string) $field_name;
			$field_props = (array) $field_props;
			$result[$field_name] = array(
				'elem' => (string) $field_props['elem'],
				'html' => $elem_creator->renderElem($field_name, $field_props),
				'label' => (isset($field_props['label']) && $field_props['label']) ? (string) $field_props['label'] : '-'
			);
		}
		return $result;
	}

}
