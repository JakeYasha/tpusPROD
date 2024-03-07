<?php

namespace App\Action;

use App\Model\Price as PriceModel;
use Sky4\Exception;

class Price extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new PriceModel());
	}

	public function findModelObject($id_price, $id_service) {
		$this->model()->reader()
				->setWhere(['AND', '`legacy_id_price` = :id_price', '`legacy_id_service` = :id_service'], [':id_price' => $id_price, ':id_service' => $id_service])
				->objectByConds();

		if (!$this->model()->exists()) {
			throw new Exception(Exception::TYPE_BAD_URL);
		}
	}

	public function findModelObjectById($id) {
		$this->model()->reader()->object($id);

		if (!$this->model()->exists()) {
			throw new Exception();
		}
	}

	public function execute() {
		throw new Exception();
	}

	/**
	 * 
	 * @return PriceModel
	 */
	public function model() {
		return parent::model();
	}

}
