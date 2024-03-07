<?php

namespace App\Classes\Controller;

use App\Model\Firm;
use App\Model\FirmManager;
use App\Model\FirmUser;
use App\Model\RedirectRule;
use App\Model\Price;
use Sky4\Exception;
use Sky4\Session;
use Sky4\Helper\CustomObject;
use Sky4\View\Layout;
use function app;
use function str;

class Front extends \Sky4\Controller\Front {

	public function afterRun() {
		if ($this->checkStatExceptions()) {
			app()->stat()->fixResponse();
		}
		return parent::afterRun();
	}

	public function beforeInit() {
		$redirect = new RedirectRule();
		$redirect->checkByUrl();

		$session = new Session();
		$this->locationByParams();
		app()->useCaptcha();

		$filters = app()->request()->processGetParams([
			'sorting' => ['type' => 'string']
		]);

		if ($filters['sorting'] !== null) {
			app()->metadata()->setCanonicalUrl(preg_replace('~[?&]sorting=[a-z]*~u', '', app()->uri()));
		}

		if ((int)app()->location()->currentId() === 643) {
			app()->location()->clear();
			throw new Exception(Exception::TYPE_BAD_URL);
		}

		// Если в регионе нет компаний - выводим 404
		// if (!app()->location()->stats('count_firms')) {
		// 	app()->location()->clear();
		// 	throw new Exception(Exception::TYPE_BAD_URL);
		// } 

		app()->breadCrumbs()->setElem(app()->location()->currentName(), app()->location()->currentId() === '76004' ? '/' : app()->location()->linkPrefix());

		return parent::beforeInit();
	}

	public function beforeRun() {
		if ($this->checkStatExceptions()) {
			app()->stat()->fixRequest(implode('-', [$this->params['controller_name'], $this->params['method_name']]), $_GET);
		}
		return parent::beforeRun();
	}

	protected function checkLayout() {
		if (!$this->layout()->getTemplate()) {
			$this->layout()->setTemplate(($this->params['controller_name'] === 'App\\Controller\\Index') ? 'index' : 'page');
		}
		return $this;
	}

	public function checkStatExceptions() {
        //if ($this->controller() instanceof FirmUser || $this->controller() instanceof FirmManager) {
		if ((isset($this->params['controller_name']) && $this->params['controller_name'] === 'App\\Controller\\FirmUser') || (isset($this->params['controller_name']) && $this->params['controller_name'] === 'App\\Controller\\FirmManager')) {
			return false;
		}
		return true;
	}

	public function locationByParams() {
		if ($this->params['controller_name'] === 'App\\Controller\\Firm' && $this->params['method_name'] === 'actionShow') {
			if (!isset($this->params['args'][1])) {
				throw new Exception(Exception::TYPE_BAD_URL);
			}

			$firm = new Firm();
			$firm->reader()
					->setWhere(['AND', '`id_firm` = :firm', '`id_service` = :service', ['OR', 'flag_is_active = :flag_is_active', ['AND', 'flag_is_active = :flag_is_not_active', 'timestamp_ratiss_updating > :timestamp_ratiss_updating']]], [':firm' => $this->params['args'][0], ':service' => $this->params['args'][1], ':flag_is_active' => 1, ':flag_is_not_active' => 0, 'timestamp_ratiss_updating' => \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m') - 6))])
					->objectByConds();

            if (isset($this->params['args'][2])) {
                $_fb = new \App\Model\FirmBranch();
                $id_firm_branch = (int)$this->params['args'][2];
                $branch = $_fb->reader()
                        ->setWhere([
                                'AND',
                                'firm_id = :firm_id',
                                'id = :firm_branch'
                            ],[
                                ':firm_id' => $firm->id(), 
                                ':firm_branch' => $id_firm_branch
                            ])
                        ->objectByConds();
                if ($branch->exists() && !$branch->isBlocked()){
                    $vals = $branch->getVals();
                    $firm->branch_id = $vals['id'];
                    $firm->flag_is_price_attached = $vals['flag_is_price_attached'];
                    unset($vals['id']);
                    $firm->setVals($vals);
                }
            }
            
			if ((int)$firm->val('id_country') === 643) {
				app()->location()->set($firm->val('id_city'));
			} else {
				app()->location()->set($firm->val('id_country').'-'.$firm->val('id_city'));
			}
		} elseif ($this->params['controller_name'] === 'App\\Controller\\Price' && $this->params['method_name'] === 'actionShow') {
			$price = new Price();
			if (isset($this->params['args'][1])) {
				$price->reader()
						->setWhere(['AND', '`legacy_id_price` = :id_price', '`legacy_id_service` = :id_service'], [':id_price' => $this->params['args'][0], ':id_service' => $this->params['args'][1]])
						->objectByConds();
			} else {
				$price->reader()->object($this->params['args'][0]);
			}


			$firm = new Firm();
			$firm->getByIdFirm($price->val('id_firm'));

			if ((int)$firm->val('id_country') === 643) {
				app()->location()->set($firm->val('id_city'));
			} else {
				app()->location()->set($firm->val('id_country').'-'.$firm->val('id_city'));
			}
		} else {
			if (isset($this->params['args'][0]) && $this->params['controller_name'] === 'App\\Controller\\Utils') {
				app()->location()->set($this->params['args'][0]);
			} else {
				app()->location()->set($this->params['location']);
			}
		}

		if (($this->params['controller_name'] === 'App\\Controller\\Catalog' || ($this->params['controller_name'] === 'App\\Controller\\Firm' && $this->params['method_name'] !== 'actionShow')) && $this->params['location'] === null) {
			app()->response()->redirect('/76004'.app()->uri(), 301);
		}

		if ($this->params['location'] === null) {
			if (app()->location()->currentId()) {
				$this->params['location'] = app()->location()->currentId();
				app()->location()->set($this->params['location']);
			} else {
				$this->params['location'] = 76004;
				app()->location()->set($this->params['location']);
			}
		} elseif ($this->params['location'] === '0') {
			app()->response()->redirect(str()->replace(app()->uri(), "/0/", "/76004/"));
		}
	}

}
