<?php

namespace App\Model;

class FirmManager extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\UserTrait,
	 Component\IdServiceTrait;

	private $manager_user_ids = null;
	private $firm = null;

	public function firm() {
		if ($this->firm === null && isset($_SESSION['_virtual_id_firm'])) {
			$this->firm = new Firm($_SESSION['_virtual_id_firm']);
		}

		return $this->firm && $this->firm->exists() ? $this->firm : new Firm();
	}

	public function hasAccess(Firm $firm) {
		$result = false;
		$manager_ids = $this->getManagerUserIds();

		if (($firm->id_manager() !== 0 && in_array($firm->id_manager(), $manager_ids)) || ($this->isSuperMan() && $this->id_service() === $firm->id_service())) {
			$result = true;
		}
		return $result;
	}

	public function getManagerUserIds() {
		if ($this->manager_user_ids === null) {
			$fm = new $this;
			$_childs = $this->reader()
					->setWhere(['AND', 'parent_node = :parent_node', 'id_user != :nil', 'parent_node != :nil'], [':parent_node' => $this->val('id_user'), ':nil' => 0])
					->rowsWithKey('id_user');

			$result_set = array_keys($_childs) + [$this->val('id_user')];
			$this->manager_user_ids = $result_set;
		}

		return $this->manager_user_ids;
	}

	//

	public function cols() {
		return [
			'id_user' => ['label' => 'ID_USER'],
			'parent_node' => ['label' => 'PARENT_USER'],
			'type' => ['label' => 'Тип'],
			'name' => ['label' => 'Имя'],
			'email' => ['label' => 'Email'],
			'last_activity_timestamp' => ['label' => 'Последний заход', 'style_class' => 'date-time']
		];
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function fields() {
		return [
			'id_user' => [
				'col' => [
					'flags' => 'not_null primary_key',
					'name' => 'id_user',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_user'
			],
			'parent_node' => [
				'elem' => 'text_field',
				'label' => 'Старший менеджер',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'email_default' => [
				'elem' => 'text_field',
				'label' => 'E-mail',
				'params' => [
					'rules' => ['length' => ['max' => 255], 'required']
				]
			],
			'type' => [
				'col' => [
					'flags' => 'not_null',
					'type' => "list('','service')",
					'default_val' => ''
				],
				'elem' => 'radio_buttons',
				'label' => 'Вид пользователя',
				'options' => ['' => 'Менеджер', 'service' => 'Сервис']
			],
		];
	}

	// Получение менеджера фирмы
	public function getByFirm(Firm $firm) {
		$this->reader()
				->setWhere(['AND', 'id_user = :id_user'], [':id_user' => $firm->val('id_manager')])
				->objectByConds();
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Менеджеры фирм';
	}

	public function filterFields() {
		return [
			'name' => [
				'elem' => 'text_field',
				'field_name' => 'name',
				'label' => 'Поиск по имени'
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'name']
		];
	}

	public function isSuperMan() {
		return (string)$this->val('type') === 'service';
	}

	public function isNewsEditor() {
		$role = new FirmManagerRole();
        $role->reader()->setWhere(['AND', 'firm_manager_id = :firm_manager_id', 'news_editor_role = :1'], [':firm_manager_id' => $this->id(), ':1' => 1])
                ->objectByConds();
        
        return $role->exists();
	}

    public function id_firm() {
		return isset($_SESSION['_virtual_id_firm']) ? (int)$_SESSION['_virtual_id_firm'] : app()->response()->redirect('/#login');
	}

}
