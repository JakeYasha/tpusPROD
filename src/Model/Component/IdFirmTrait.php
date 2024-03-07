<?php

namespace App\Model\Component;

trait IdFirmTrait {

	private $firm = null;

	/**
	 *
	 * @return IdFirm
	 */
	public function idFirmComponent() {
		return $this->component('IdFirm');
	}

	public function id_firm() {
		return (int) $this->val('id_firm');
	}

	public function id_service() {
		if ($this instanceof \App\Model\Firm || $this instanceof \App\Model\FirmBranch) {
			return (int) $this->val('id_service');
		}
		return (int) $this->firm()->val('id_service');
	}

	public function getByIdFirm($id_firm) {
		if ($this instanceof \App\Model\Firm) {
			return $this->reader()->object($id_firm);
		}

		return $this->reader()->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => $id_firm])->objectByConds();
	}
	
	public function getByIdFirmAndIdService($id_firm, $id_service) {
		return $this->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service'], [':id_firm' => $id_firm, ':id_service' => $id_service])->objectByConds();
	}

	public function getByFirm(\App\Model\Firm $firm) {
		return $this->getByIdFirm($firm->id());
	}

	/**
	 * 
	 * @return \App\Model\Firm
	 */
	public function firm() {
		if (!$this instanceof \App\Model\Firm && !$this instanceof \App\Model\Price) {
			if ($this->firm === null) {
				$this->firm = new \App\Model\Firm();
				$this->firm->reader()
						->setWhere(['AND', 'id = :id'], [':id' => $this->val('id_firm')])
						->objectByConds();
			}
			return $this->firm;
		}

		if ($this instanceof \App\Model\Price) {
			if ($this->firm === null) {
				$this->firm = $this->getFirm();
			}
			return $this->firm;
		}

		return $this;
	}

	public function filter() {
		if ($this->filter === null) {
			$this->filter = new \App\Model\FirmFilter($this);
		}

		return $this->filter;
	}

}
