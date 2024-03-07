<?php

namespace App\Model\StsService;

use App\Model\StsService;
use Sky4\Model\Form;

class FormAdd extends Form {

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
			'action' => '/sts-service/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$result = [];
		$model = new StsService();
		$fields = $model->getFields();

		$fields['name']['label'] = 'Название';
		$fields['id_service']['elem'] = 'hidden_field';
		$fields['email']['label'] = 'Электронная почта для отправки писем-уведомлений с сайта';
		$fields['phone']['label'] = 'Телефон';
		$fields['id_service']['label'] = null;

		$result['id_service'] = $fields['id_service'];
		$result['name'] = $fields['name'];
		$result['email'] = $fields['email'];
		$result['phone'] = $fields['phone'];
		$result['address'] = $fields['address'];
		$result['web'] = $fields['web'];
		$result['mode_work'] = $fields['mode_work'];
		$result['info'] = $fields['info'];

		$result['vk_link'] = $fields['vk_link'];
		$result['fb_link'] = $fields['fb_link'];
		$result['tw_link'] = $fields['tw_link'];
		$result['in_link'] = $fields['in_link'];
		$result['ok_link'] = $fields['ok_link'];
		$result['gp_link'] = $fields['gp_link'];
		$result['yt_link'] = $fields['yt_link'];

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', 'Информация о региональном представительстве')
						->set('sub_heading', 'Здесь вы можете изменить информацию о региональном представительстве, которая показывается для страниц вашего региона')
						->setTemplate('common_form_no_captcha', 'forms')
						->render();
	}

}
