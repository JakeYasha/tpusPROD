<?php

/**
 * @created Apr 26, 2017
 * @author Dmitriy Mitrofanov <d.i.mitrofanov@gmail.com>
 */
namespace App\Classes;

use App\Model\CurrentRegionCity;
use App\Model\StsCity;
use App\Model\StsCountry;
use App\Model\StsRegionCountry;
use Sky4\Container;
use Sky4\Exception;

class Location {

	private $session_var = '__app_location';
	private $currentId = null;

	public function clear() {
		$_SESSION[$this->session_var] = [];
	}

	public function set($id) {
		//$this->clear();

		if ((string) $id === 'index') {
			$id = 76004;
		}

		if ((string) $id === '') {
			if (!isset($_GET['url'])) {
				$id = 76004;
			} else {
				if (isset($_SESSION[$this->session_var]['last_id'])) {
					$id = $_SESSION[$this->session_var]['last_id'];
				} else {
					$id = 76004;
				}
			}
		}

		$this->currentId = $id;
		if (!isset($_SESSION[$this->session_var][$id])) {
			$this->clear();
		} else {
			return $this;
		}

		if (str()->pos($id, '-') !== false) {
			$arr = explode('-', $id);
			$_SESSION[$this->session_var][$id]['country']['id'] = $arr[0];
			$_SESSION[$this->session_var][$id]['city']['id'] = isset($arr[1]) ? $arr[1] : 0;
			$_SESSION[$this->session_var][$id]['cities'] = isset($arr[1]) ? [$arr[1] => true] : []; //$this->getCountryCities($arr[0]);
		} else {
			$_SESSION[$this->session_var][$id]['country']['id'] = 643;

			if ($id || $id === 0) {
				if ($id === 0) {
					$_SESSION[$this->session_var][$id]['region']['id'] = 0;
					$_SESSION[$this->session_var][$id]['country']['id'] = 0;
					$_SESSION[$this->session_var][$id]['city']['id'] = 0;

					$_SESSION[$this->session_var][$id]['cities'] = $this->getRegionCities();
				} else {
					if ($id < 999) {
						$_SESSION[$this->session_var][$id]['region']['id'] = $id;
						$_SESSION[$this->session_var][$id]['cities'] = $this->getRegionCities($id);
					} else {
						$_SESSION[$this->session_var][$id]['country']['id'] = 643;
						$_SESSION[$this->session_var][$id]['city']['id'] = $id;
						$_SESSION[$this->session_var][$id]['cities'] = [$id => ''];
						$_SESSION[$this->session_var][$id]['region']['id'] = $this->city()->val('id_region_country');
					}
				}
			} else {
				$_SESSION[$this->session_var][$id]['country']['id'] = 643;
				$_SESSION[$this->session_var][$id]['region']['id'] = 76;
				$_SESSION[$this->session_var][$id]['city']['id'] = 76004;
				$_SESSION[$this->session_var][$id]['cities'] = [76004 => 'Ярославль'];
			}
		}

		if ($id !== '') {
			$_SESSION[$this->session_var]['last_id'] = $id;
		}

		$firm_city = new \App\Model\FirmCity();
		$city_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($_SESSION[$this->session_var][$id]['cities']), 'id_city');
		$_SESSION[$this->session_var][$this->currentId]['firm_ids'] = array_keys($firm_city->reader()->setSelect(['id_firm'])->setWhere($city_conds['where'], $city_conds['params'])->rowsWithKey('id_firm'));
        
        //Выбираем id фирм с филиалом/филиалами в выбранном городе
        $_firm_branch = new \App\Model\FirmBranch();
        $_firm_ids = array_unique(array_keys($_firm_branch->reader()
                ->setSelect(['firm_id'])
                ->setWhere($city_conds['where'], $city_conds['params'])
                ->rowsWithKey('firm_id')));
        if ($_firm_ids) {
            $_SESSION[$this->session_var][$this->currentId]['firm_ids'] = array_merge($_SESSION[$this->session_var][$this->currentId]['firm_ids'], $_firm_ids);
        }
        //Сохраним на всякий id фирм, у которых есть филиалы в выбранном городе
        $_SESSION[$this->session_var][$this->currentId]['firm_ids_has_branches'] = $_firm_ids;
        //--------------------------------------------------------

		return $this;
	}

	/**
	 * @return StsCity
	 */
	public function city() {
		if (isset($_SESSION[$this->session_var]['currentCity']) && $_SESSION[$this->session_var]['currentCity'] instanceof StsCity) {
			return $_SESSION[$this->session_var]['currentCity'];
		} elseif (isset($_SESSION[$this->session_var][$this->currentId]['city']['id'])) {
			$city = new StsCity($_SESSION[$this->session_var][$this->currentId]['city']['id']);
			$city->setVal('name', str()->firstCharToUpper(str()->toLower($city->val('name'))));
			$_SESSION[$this->session_var]['currentCity'] = $city;
			return $city;
		} else {
			$_SESSION[$this->session_var]['currentCity'] = new StsCity();
			return $_SESSION[$this->session_var]['currentCity'];
		}
		throw new Exception('Bad usage of ALocation');
	}

	/**
	 * @return StsCountry
	 */
	public function country() {
		if (isset($_SESSION[$this->session_var]['currentCountry']) && $_SESSION[$this->session_var]['currentCountry'] instanceof StsCountry) {
			return $_SESSION[$this->session_var]['currentCountry'];
		} elseif (isset($_SESSION[$this->session_var][$this->currentId]['country']['id'])) {
			$country = new StsCountry($_SESSION[$this->session_var][$this->currentId]['country']['id']);
			$country->setVal('name', str()->firstCharToUpper(str()->toLower($country->val('name'))));
			$_SESSION[$this->session_var]['currentCountry'] = $country;
			return $country;
		} else {
			$_SESSION[$this->session_var]['currentCity'] = new StsCountry();
			return isset($_SESSION[$this->session_var]['currentCountry']) ? $_SESSION[$this->session_var]['currentCountry'] : new StsCountry();
		}
		throw new Exception('Bad usage of ALocation');
	}

	/**
	 * @return StsRegionCountry
	 */
	public function region() {
		if (isset($_SESSION[$this->session_var]['currentRegion']) && $_SESSION[$this->session_var]['currentRegion'] instanceof StsRegionCountry) {
			return $_SESSION[$this->session_var]['currentRegion'];
		} elseif (isset($_SESSION[$this->session_var][$this->currentId]['region']['id'])) {
			$region = new StsRegionCountry();
			$region->reader()
					->setWhere(['AND', 'id_region_country = :id_region_country', 'id_country = :id_country'], [':id_region_country' => $_SESSION[$this->session_var][$this->currentId]['region']['id'], ':id_country' => $this->country()->id()])
					->objectByConds();

			$region->setVal('name', str()->firstCharToUpper(str()->toLower($region->val('name'))));
			$_SESSION[$this->session_var]['currentRegion'] = $region;
			return $region;
		} else {
			$_SESSION[$this->session_var]['currentRegion'] = new StsRegionCountry();
			return $_SESSION[$this->session_var]['currentRegion'];
		}
		throw new Exception('Bad usage of ALocation');
	}

	public function changeLink($id) {
		return '/utils/change-location/' . encode($id) . '/';
	}

	public function currentId() {
		$result = 0;
		if ((int) $this->country()->id() === 643) {
			if ($this->city()->exists()) {
				$result = $this->city()->id();
			} elseif ($this->region()->exists()) {
				$result = $this->region()->id();
			} else if ($this->country()->exists()) {
				$result = $this->country()->id();
			}
		} else {
			if ($this->city()->exists()) {
				$result = $this->city()->id();
			} elseif ($this->region()->exists()) {
				$result = $this->region()->id();
			}

			$result = $this->country()->id() . '-' . $result;
		}

		if ($result === '-0') {
			$result = '';
		}

		return $result;
	}

	public function currentFullId() {
		$country = $this->country()->id() == 643 ? false : $this->country()->id();
		$city = $this->city()->id();
		$region = $this->region()->id();
		$result = [];
		if ($country) {
			$result[] = $country;
		}
		if ($city) {
			$result[] = $city;
		} else {
			if ($region) {
				$result[] = $region;
			}
		}


		return $result ? implode('-', $result) : '';
	}

	public function currentName($case = null) {
		if ($case !== null) {
			return $this->currentCaseName($case);
		}

		if ($this->city()->exists()) return trim($this->city()->name());
		if ($this->region()->exists()) return trim($this->region()->name());
		if ($this->country()->exists()) return trim($this->country()->name());

		return 'Все города';
	}

	public function currentCaseName($case) {
		if ($this->city()->exists()) {
			$name = $this->city()->name();
			$type = 'city';
		} elseif ($this->region()->exists()) {
			$name = $this->region()->name();
			$type = 'region';
		} elseif ($this->country()->exists()) {
			$name = $this->country()->name();
			$type = 'country';
		} else {
			$name = 'Все города';
			$type = '';
		}

		$name = trim($name);
		$list = Container::getList('City', 'getList');
		$cases = isset($list[str()->toLower($name)]) ? $list[str()->toLower($name)] : [];
		$result = $name;

		if ($type === 'city') {
			if (isset($cases[$case])) {
				if ($case == 'prepositional') {
					$result = ' в ' . $cases['prepositional'];
				} else {
					$result = ' ' . $cases['genitive'];
				}
			} else {
				$result = 'г. ' . $name;
			}
		} elseif ($type === 'region') {
			if ($case == 'prepositional') {
				$result = ' в регионе ' . $name;
			} elseif ($case == 'genitive') {
				$result = ' региона ' . $name;
			} else {
				$result = ' для региона ' . $name;
			}
		}

		return $result;
	}

	public function linkPrefix() {
		$country = $this->country()->id() == 643 ? false : $this->country()->id();
		$city = $this->city()->id();
		$region = $this->region()->id();
		$result = [];
		if ($country) {
			$result[] = $country;
		}
		if ($city) {
			$result[] = $city;
		} else {
			if ($region) {
				$result[] = $region;
			}
		}


		return $result ? '/' . implode('-', $result) : '';
	}

	public function link($url, $fixed_prefix = null) {
		$result = '';
		$prefix = $this->linkPrefix();
		if ($url === '/') $url = '';
		if ($url === '' && $prefix === '/76004') {
			$result = '/';
		} else {
			$result = $fixed_prefix === null ? $prefix . $url : '/' . $fixed_prefix . $url;
		}
		return $result;
	}

	/**
	 * Searches for location-based statistic values from CurrentRegionCity
	 * @param string $param field of CurrentRegionCity class
	 * @return string
	 */
	public function stats($param = null) {
		$model = new CurrentRegionCity();
		$id_country = $this->country()->id();
		$id_city = $this->city()->id();
		$id_region = $this->region()->id();

		$where = [];
		$params = [];
		if ($id_city) {
			$where = ['AND', '`id_city` = :id_city', '`id_country` = :id_country'];
			$params = [':id_city' => $id_city, ':id_country' => $id_country];
		} elseif ($id_region) {
			$where = ['AND', '`id_region` = :id_region', '`id_country` = :id_country', '`id_city` = :nil'];
			$params = [':id_region' => $id_region, ':id_country' => $id_country, ':nil' => 0];
		} elseif ($id_country) {
			$where = ['AND', '`id_country` = :id_country', '`id_city` = :nil', '`id_region` = :nil'];
			$params = [':id_country' => $id_country, ':nil' => 0];
		} else {
			$where = ['AND', '`id_country` = :nil', '`id_city` = :nil', '`id_region` = :nil'];
			$params = [':nil' => 0];
		}

		$model->reader()
				->setWhere($where, $params)
				->objectByConds();

		$_SESSION[$this->session_var][$this->currentId]['stats'] = $model;
		return $param === null ? $model : $model->val($param);
	}

	public function getRegionCities($id_region = null) {
		if ($id_region === null) {
			$conds = ['AND', '`id_city` != :nil'];
			$params = [':nil' => 0];
		} else {
			$conds = ['AND', '`id_city` != :nil', '`id_region` = :id_region'];
			$params = [':nil' => 0, ':id_region' => $id_region];
		}

		$rows = App::db()->query()
				->setSelect(['id_city', 'name'])
				->setFrom(['current_region_city'])
				->setWhere($conds, $params)
				->select();

		$cities = [];
		foreach ($rows as $row) {
			$cities[$row['id_city']] = $row['name'];
		}

		return $cities;
	}

	public function getCountryCities($id_country) {

		$conds = ['AND', '`id_city` != :nil', '`id_country` = :id_country'];
		$params = [':nil' => 0, ':id_country' => $id_country];


		$rows = App::db()->query()
				->setSelect(['id_city', 'name'])
				->setFrom(['current_region_city'])
				->setWhere($conds, $params)
				->select();

		$cities = [];
		foreach ($rows as $row) {
			$cities[$row['id_city']] = $row['name'];
		}

		return $cities;
	}

	public function getCityIds() {
		return array_keys($_SESSION[$this->session_var][$this->currentId]['cities']);
	}

	public function getFirmIds($id_city = null) {
		if ($id_city === null) {
			return $_SESSION[$this->session_var][$this->currentId]['firm_ids'];
		} else {
			if (!isset($_SESSION[$this->session_var][$id_city]['firm_ids']) || !$_SESSION[$this->session_var][$id_city]['firm_ids']) {
				$firm_city = new \App\Model\FirmCity();
				$_SESSION[$this->session_var][$id_city]['firm_ids'] = array_keys($firm_city->reader()->setSelect(['id_firm'])->setWhere(['AND', 'id_city = :id_city'], [':id_city' => $id_city])->rowsWithKey('id_firm'));
			}

			return $_SESSION[$this->session_var][$id_city]['firm_ids'];
		}
	}
    
    //id фирм, имеющих филиалы для текущего города
    public function getFirmIdsHasBranches($id_city = null) {
		if ($id_city === null) {
			return $_SESSION[$this->session_var][$this->currentId]['firm_ids_has_branches'];
		} else {
			if (!isset($_SESSION[$this->session_var][$id_city]['firm_ids_has_branches']) || !$_SESSION[$this->session_var][$id_city]['firm_ids_has_branches']) {
				$firm_branch = new \App\Model\FirmBranch();
				$_SESSION[$this->session_var][$id_city]['firm_ids_has_branches'] = array_keys($firm_branch->reader()->setSelect(['firm_id'])->setWhere(['AND', 'id_city = :id_city'], [':id_city' => $id_city])->rowsWithKey('firm_id'));
			}

			return $_SESSION[$this->session_var][$id_city]['firm_ids_has_branches'];
		}
	}
    
    public function getFirmIdsByService($id_service = null) {
        if ($id_service !== null) {
            $firm = new \App\Model\Firm();
            return array_keys($firm->reader()
                    ->setSelect(['id'])
                    ->setWhere([
                            'AND', 
                            'id_service = :id_service', 
                            'flag_is_active = :flag_is_active'
                        ], [
                            ':id_service' => $id_service, 
                            ':flag_is_active' => 1
                        ])
                    ->rowsWithKey('id'));
        } else {
            return [];
        }
    }

	public function getRegionId() {
        return $this->region()->val('id_region_country');
	}

	public function getCityId() {
		return $_SESSION[$this->session_var]['currentCity']->val('id_city');
	}

}
