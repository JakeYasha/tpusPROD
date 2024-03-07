<?php

namespace App\Model\NewFirmUser;

use App\Model\FirmUser;

class ManagerForm extends \Sky4\Model\Form {

	public function __construct(\Sky4\Model $model = null, $params = null) {
		if ($model === null) {
			$model = new FirmUser();
		}

		parent::__construct($model, $params);
	}

	public function editableFieldsNames() {
		return ['email', 'id_firm', 'name'];
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Создать',
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
			'action' => '/firm-manager/submit/new-firm-user/?redirect=' . urlencode('/firm-manager/'),
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$result = [];

		$fields = $this->model()->getFields();
		$fields['email']['attrs'] = ['class' => 'tooltip', 'title' => 'По умолчанию указан email фирмы. Если у фирмы нет email или клиент хочет входить в личный кабинет под другим email - укажите его выше. Email фирмы не будет перезаписан.'];
		$fields['email']['params']['rules'] = ['length' => ['max' => 50], 'email'];
		$fields['name']['elem'] = 'hidden_field';
		$fields['name']['default_val'] = 'Пользователь';

		$result['email'] = $fields['email'];
		$result['name'] = $fields['name'];
		$result['id_firm'] = $fields['id_firm'];

		return $result;
	}

	public function prepareRedirectAttrs($attrs) {
		$params = app()->request()->processGetParams([
			'page' => 'int',
			'query' => 'string',
			'sorting' => 'string'
		]);

		$attr_splitter = urlencode('?');

		if ($params['page']) {
			$attrs['action'] .= $attr_splitter . urlencode('page=' . $params['page']);
			$attr_splitter = urlencode('&');
		}
		if ($params['query']) {
			$attrs['action'] .= $attr_splitter . urlencode('query=' . $params['query']);
			$attr_splitter = urlencode('&');
		}
		if ($params['sorting']) {
			$attrs['action'] .= $attr_splitter . urlencode('sorting=' . $params['sorting']);
			$attr_splitter = urlencode('&');
		}
		return $attrs;
	}

	public function render($vals, $heading = null) {
		$this->setFieldProp('email', 'default_val', $vals['email']);
		$this->setFieldProp('id_firm', 'val', $vals['id_firm']);
		return $this->view()
						->set('attrs', $this->prepareRedirectAttrs($this->getAttrs()))
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('id_firm', $vals['id_firm'])
						->set('email', $vals['email'])
						->set('sub_heading', '')
						->setTemplate('new_firm_user_manager_form', 'forms')
						->render();
	}

}
