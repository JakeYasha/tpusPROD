<?php

namespace App\Model\FirmUser;

use App\Model\FirmUser;

class ManagerForm extends \Sky4\Model\Form {

	public function __construct(\Sky4\Model $model = null, $params = null) {
		if ($model === null) {
			$model = new FirmUser();
		}

		parent::__construct($model, $params);
	}

	public function editableFieldsNames() {
		return ['email', 'id'];
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Сохранить',
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
			'action' => '/firm-manager/submit/firm-user/?redirect=' . urlencode('/firm-manager/'),
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$result = [];

		$fields = $this->model()->getFields();
		$fields['password']['label'] = 'Новый пароль';
		//$fields['email']['rules'] = ['email'];
		$fields['id']['elem'] = 'hidden_field';

		$result['id'] = $fields['id'];
		$result['email'] = $fields['email'];
		//$result['timestamp_inserting'] = $fields['email'];
		//$result['password'] = $fields['password'];

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
		return $this->view()
						->set('attrs', $this->prepareRedirectAttrs($this->getAttrs()))
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('id_firm_user', $this->model()->id())
						->set('id_firm', $vals['id_firm'])
						->set('has_last_logon_timestamp', ($this->model()->exists() && \Sky4\Helper\DeprecatedDateTime::nil() !== $this->model()->val('last_logon_timestamp')))
						->set('has_firm_user', $this->model()->exists())
						->set('last_logon_timestamp', date('d.m.Y H:i:s', \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->model()->val('last_logon_timestamp'))))
						->set('sub_heading', '')
						->setTemplate('firm_user_manager_form', 'forms')
						->render();
	}

}
