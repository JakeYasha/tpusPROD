<?php

namespace App\Model\FirmUser;

class Form extends \Sky4\Model\Form {

	public function editableFieldsNames() {
		return [
			'email',
//			'login',
			'name',
			'password',
			'virtual_id_firm',
		];
	}

	public function fields() {
		$fields = $this->model()->getFields();
		$fields['password']['label'] = 'Новый пароль';

		return $fields;
	}

	public function structure() {
		$structure = [
			['type' => 'field', 'name' => 'virtual_id_firm'],
			['type' => 'field', 'name' => 'name'],
			['type' => 'label', 'text' => 'Аккаунт'],
			['type' => 'field', 'name' => 'email'],
//			['type' => 'field', 'name' => 'login'],
			['type' => 'field', 'name' => 'password'],
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
