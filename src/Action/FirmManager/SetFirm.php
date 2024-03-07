<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\FirmUser;
use function app;

class SetFirm extends FirmManager {

	protected $firm = null;

	public function execute($id_firm) {
		$this->params = app()->request()->processGetParams([
			'redirect' => ['type' => 'string']
		]);

		$redirect = $this->params['redirect'] ? $this->params['redirect'] : '/firm-user/';

		$firm = new \App\Model\Firm();
		$firm->reader()->object($id_firm);

		$this->setFirm($firm, true);

		if ($this->firm !== null) {
			$fu = new FirmUser();
			$fu->getByIdFirm($this->firm->id());
			if ($fu->exists()) {
				if (isset($_SESSION['_virtual_id_firm'])) {
					unset($_SESSION['_virtual_id_firm']);
				}
				$fu->userComponent()->saveInSession();
			} else {
				if (app()->firmUser()->exists() && app()->firmUser() instanceof FirmUser) {
					app()->firmUser()->userComponent()->removeFromSession();
				}

				//для фирм у кого нет своего пользователя
				$_SESSION['_virtual_id_firm'] = $this->firm->id();
			}
			app()->response()->redirect($redirect);
		}
		return $this;
	}

	private function setFirm(\App\Model\Firm $firm, $replace = false) {
		if ($this->firm === null || $replace) {
			if ($firm->exists()) {
				if (app()->firmManager()->hasAccess($firm)) {
					$this->firm = $firm;
				} else {
					throw new \Sky4\Exception();
				}
			}
		}

		return $this;
	}

}
