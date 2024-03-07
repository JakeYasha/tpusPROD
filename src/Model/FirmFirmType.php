<?php

namespace App\Model;

class FirmFirmType extends \Sky4\Model\Composite {

	use Component\ActiveTrait;

	private $table = 'firm_firm_type';

	public function idFieldsNames() {
		return ['id_firm'];
	}

	public function fields() {
		return [
			'id_firm' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null unsigned',
					'name' => 'id_firm',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_firm'
			],
			'id_type' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null unsigned',
					'name' => 'id_type',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_type'
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID города',
				'params' => [
					'rules' => ['int']
				]
			],
		];
	}

	/**
	 * 
	 * @param Firm $firm
	 * @return FirmType
	 */
	public function getCatalogByFirm(Firm $firm) {
		$where = [
			'AND',
			'id_firm = :id_firm',
			'id_city = :id_city'
		];

		$params = [
			':id_firm' => $firm->id(),
			':id_city' => $firm->val('id_city')
		];

		$this->reader()
				->setWhere($where, $params)
				->setOrderBy('id_type DESC')
				->objectByConds();

		$firm_type = new FirmType($this->val('id_type'));

		return $firm_type;
	}

	public function getByFirm(Firm $firm) {
		$where = [
			'AND',
			'id_firm = :id_firm',
			'id_city = :id_city'
		];

		$params = [
			':id_firm' => $firm->id(),
			':id_city' => $firm->val('id_city')
		];

		return $this->reader()
						->setWhere($where, $params)
						->rowsWithKey('id_type');
	}

	public function getByFirmIds($firm_ids) {
		$conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($firm_ids, 'id_firm');
		$where = [
			'AND',
			$conds['where']
		];

		$params = $conds['params'];

		return $this->reader()
						->setWhere($where, $params)
						->rows();
	}

	public function table() {
		return $this->table;
	}

	public function setTable($table) {
		$this->table = $table;
	}

	public function getAnalogsByTypes($id_types, Firm $exceptFirm) {
		$result = [];
		if ($id_types) {
			$query = 'SELECT 
					sf.`id`
				FROM `firm_firm_type` fft
				LEFT JOIN `firm` sf ON sf.`id` = fft.`id_firm`
				WHERE
					sf.`flag_is_active` = 1 AND 
					fft.`id_type` IN ('.implode(",", $id_types).') AND 
					fft.`id_city` = '.$exceptFirm->val('id_city').' AND 
					fft.`id_firm` != '.$exceptFirm->id().'
				GROUP BY fft.`id_firm`
				ORDER BY sf.`rating` DESC, RAND() LIMIT 7';

			$rows = app()->db()->query()
					->setText($query)
					->fetch();

			$ids = [];
			foreach ($rows as $row) {
				$ids[] = $row['id'];
			}

			if ($ids) {
				$firm = new Firm();
				$result = $firm->reader()->objectsByIds($ids);
			}
		}

		return $result;
	}

}
