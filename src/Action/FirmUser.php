<?php

namespace App\Action;

use App\Classes\Action;
use App\Model\Firm;
use App\Model\FirmUser as FirmUserModel;
use function app;

class FirmUser extends Action {

	protected $firm = null;
	protected $html_mode = null;
	protected $filters = null;
	protected static $resources_loaded = null;

	public function __construct() {
		parent::__construct();
        
        $vals = app()->request()->processGetParams([
			'control_number' => ['type' => 'string']
		]);
        
        if (!$vals['control_number']) {
            if (!(app()->firmUser()->exists() || (app()->firmManager()->exists()))) {
                app()->response()->redirect('/firm-user/login/');
            }

            $this->setModel(new FirmUserModel());
            app()->frontController()->layout()->setTemplate('lk');
            app()->breadCrumbs()
                    ->removeElem(0);

            if (app()->firmManager()->exists()) {
                app()->breadCrumbs()
                        ->setElem('Главная', '/firm-manager/');
            }

            if (self::$resources_loaded === null) {
                $js_files = \App\Controller\Common::getFirmUserJsFiles();
                foreach ($js_files as $url) {
                    app()->metadata()->setJsFile($url);
                }
                self::$resources_loaded = true;
            }

            app()->breadCrumbs()
                    ->setElem('Личный кабинет', self::link('/'));
        }
	}

	public function execute() {
		$this->text()->getByLink('firm-user/index');
		app()->metadata()->setFromModel($this->text());

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('text', $this->text())
				->setTemplate('index')
				->save();
	}

	/**
	 * 
	 * @return FirmUserModel
	 */
	public function model() {
		return parent::model();
	}

	/**
	 * 
	 * @return Firm
	 */
	public function firm() {
		if ($this->firm === null) {
			if (app()->firmManager()->exists() && isset($_SESSION['_virtual_id_firm'])) {
				$firm = new Firm();
				$firm->getByIdFirm($_SESSION['_virtual_id_firm']);

				if ($firm->exists()) {
					if (app()->firmManager()->isSuperMan()) {
						if ($firm->id_service() === app()->firmManager()->id_service()) {
							$this->firm = $firm;
						}
					} elseif (app()->firmManager()->hasAccess($firm)) {
						$this->firm = $firm;
					}
				}
			} else {
				$firm = new Firm();
				$firm->getByIdFirm(app()->firmUser()->id_firm());
				$this->firm = $firm;
			}
		}

		return $this->firm;
	}

	protected static function getDatesBlock($url, $filters) {
		if (isset($filters['page'])) unset($filters['page']);
		if ($filters['group'] === 'months') {
			$max_timestamp = mktime(0, 0, 0, date('m'), 1);
			if ((int)$filters['t_start'] === (int)$max_timestamp) {
				$visible[] = mktime(0, 0, 0, date('m', strtotime('-2 month', $filters['t_start'])), 1, date('Y', strtotime('-2 month', $filters['t_start'])));
			}
			$visible[] = mktime(0, 0, 0, date('m', strtotime('-1 month', $filters['t_start'])), 1, date('Y', strtotime('-1 month', $filters['t_start'])));
			$visible[] = mktime(0, 0, 0, date('m', strtotime('now', $filters['t_start'])), 1, date('Y', strtotime('now', $filters['t_start'])));
			$visible[] = mktime(0, 0, 0, date('m', strtotime('+1 month', $filters['t_start'])), 1, date('Y', strtotime('+1 month', $filters['t_start'])));

			foreach ($visible as $timestamp) {
				if ($timestamp <= $max_timestamp) {
					$dates_block[] = [
						'timestamp' => $timestamp,
						'name' => str()->firstCharToUpper(\Sky4\Helper\DeprecatedDateTime::monthName(\Sky4\Helper\DeprecatedDateTime::fromTimestamp($timestamp))).' '.date('Y', $timestamp),
						'link' => self::link($url, $filters, ['t_start' => $timestamp, 't_end' => \Sky4\Helper\DeprecatedDateTime::toTimestamp(\Sky4\Helper\DeprecatedDateTime::shiftMonths(+1, \Sky4\Helper\DeprecatedDateTime::fromTimestamp($timestamp)))]),
						'active' => (int)$filters['t_start'] === $timestamp
					];
				}
			}
		} else {
			$max_timestamp = strtotime('last monday', strtotime('tomorrow'));
			if ((int)$filters['t_start'] === (int)$max_timestamp) {
				$visible[] = strtotime('-2 week', $max_timestamp);
			}
			$visible[] = strtotime('-1 week', $filters['t_start']);
			$visible[] = $filters['t_start'];
			$visible[] = strtotime('+1 week', $filters['t_start']);

			foreach ($visible as $timestamp) {
				if ($timestamp <= $max_timestamp) {
					$dates_block[] = [
						'timestamp' => $timestamp,
						'name' => 'с '.date('d ', $timestamp).\Sky4\Helper\DeprecatedDateTime::monthName(\Sky4\Helper\DeprecatedDateTime::fromTimestamp($timestamp), 1).' '.date('Y', $timestamp),
						'link' => self::link($url, $filters, ['t_start' => $timestamp, 't_end' => \Sky4\Helper\DeprecatedDateTime::toTimestamp(\Sky4\Helper\DeprecatedDateTime::shiftWeeks(1, \Sky4\Helper\DeprecatedDateTime::fromTimestamp($timestamp)))]),
						'active' => (int)$filters['t_start'] === $timestamp
					];
				}
			}
		}

		return [$dates_block, $visible];
	}

	protected function updateTimestamps($model_alias) {
		if (!app()->firmManager()->exists()) {
			switch ($model_alias) {
				case 'feedback' : app()->firmUserTimestamp()->touchFeedback();
					break;
				case 'request' : app()->firmUserTimestamp()->touchRequest();
					break;
				case 'review' : app()->firmUserTimestamp()->touchReviews();
					break;
			}
		}
		return $this;
	}

	protected static function initFilters($filters) {
		if ($filters['t_start'] === null) {
			$filters['t_start'] = mktime(0, 0, 0, date('m'), 1);
		}

		if ($filters['t_end'] === null) {
			$filters['t_end'] = mktime(23, 59, 59);
		}

		if ($filters['group'] === null) {
			$filters['group'] = 'months';
		}

		if ($filters['group'] === 'months') {
			$filters['t_start'] = mktime(0, 0, 0, date('m', $filters['t_start']), 1, date('Y', strtotime('now', $filters['t_start'])));
			$filters['t_end'] = mktime(0, 0, 0, date('m', $filters['t_start']), date('t', $filters['t_start']), date('Y', strtotime('now', $filters['t_start'])));
		} else {
			$filters['t_start'] = strtotime('last monday', strtotime('tomorrow', $filters['t_start']));
			$filters['t_end'] = strtotime('+7 days', strtotime('last monday', strtotime('tomorrow', $filters['t_start'])));
		}

		if (!isset($filters['html_mode'])) {
			$filters['html_mode'] = false;
		}

		return $filters;
	}

	public static function link($link, $filters = [], $current = []) {
		return app()->linkFilter('/firm-user'.$link, $filters, $current);
	}

	protected function checkModelAccess(\Sky4\Model $model) {
		if ($model->id_service() === $this->firm()->id_service() && $model->id_firm() === (int)$this->firm()->id()) {
			return true;
		}

		throw new \Sky4\Exception();
	}

	protected function delete(\Sky4\Model $object, $url = '/firm-user/') {
		if ($object->exists() && $object->id_firm() === $this->firm()->id()) {
			$object->delete();
			app()->response()->redirect($url.'?success_delete');
		}

		app()->response()->redirect($url.'/?fail_delete');
	}

	protected function setHtmlMode($mode) {
		$this->html_mode = (bool)$mode;
		return $this;
	}

	protected function isHtmlMode() {
		return $this->html_mode === true;
	}

	protected function setFilters($filters) {
		$this->filters = (array)$filters;
		return $this;
	}

	protected function getFilters() {
		return $this->filters;
	}

}
