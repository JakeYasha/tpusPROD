<?php

namespace App\Model\FirmManager;

class CmsForm extends \Sky4\Model\Form {

	public function editableFieldsNames() {
		return [
			'email_default',
			'id_user',
			'email',
			'name',
			'password',
			'id_service',
			'id_user',
			'parent_node',
			'type'
		];
	}

	public function fields() {
		$fields = $this->model()->getFields();
		$fields['email_default']['elem'] = 'text_field';
		$fields['email_default']['label'] = 'Email для сообщений';
		$fields['id_user']['elem'] = 'text_field';
		$fields['id_user']['label'] = 'ID_USER';
		$fields['password']['elem'] = 'password_field';
		$fields['password']['label'] = 'Новый пароль';
		$fields['parent_node']['elem'] = 'text_field';
		$fields['parent_node']['label'] = 'PARENT_USER';
		$fields['id_service']['elem'] = 'text_field';
		$fields['id_service']['label'] = 'ID_SERVICE';

		return $fields;
	}

	public function structure() {
		$structure = [
			['type' => 'field', 'name' => 'name'],
			['type' => 'field', 'name' => 'email_default'],
			['type' => 'label', 'text' => 'Аккаунт'],
			['type' => 'field', 'name' => 'email'],
			['type' => 'field', 'name' => 'password'],
			['type' => 'field', 'name' => 'id_service'],
			['type' => 'field', 'name' => 'type'],
			['type' => 'label', 'text' => 'Связи'],
			['type' => 'field', 'name' => 'id_user'],
			['type' => 'field', 'name' => 'parent_node']
		];
		if ($this->model()->exists()) {
			$structure = array_merge($structure, [
				['type' => 'label', 'text' => 'Активность'],
				['type' => 'field', 'name' => 'last_activity_timestamp'],
				['type' => 'field', 'name' => 'last_activity_ip_addr'],
				['type' => 'field', 'name' => 'last_logon_timestamp'],
				['type' => 'field', 'name' => 'last_logon_ip_addr'],
				['type' => 'field', 'name' => 'prev_logon_timestamp'],
				['type' => 'field', 'name' => 'prev_logon_ip_addr']
			]);
		}
		return $structure;
	}

}
