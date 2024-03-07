<?php

namespace App\Model;

use App\Model\Component\IdTrait;
use Sky4\Db\ColType;
use Sky4\Model\Composite;

class RedirectRule extends Composite {

	use IdTrait;

	public function cols() {
		return [
			'id' => ['label' => 'КОД'],
			'from' => ['label' => 'URL запроса'],
		];
	}

	public function fields() {
		return [
			'from' => [
				'col' => ColType::getString(1000),
				'elem' => 'text_field',
				'label' => 'Откуда',
				'params' => [
					'rules' => ['length' => ['max' => 1000, 'min' => 1], 'required']
				]
			],
			'to' => [
				'col' => ColType::getString(1000),
				'elem' => 'text_field',
				'label' => 'Куда',
				'params' => [
					'rules' => ['length' => ['max' => 1000, 'min' => 1], 'required']
				]
			],
			'code' => [
				'col' => ColType::getString(1000),
				'elem' => 'text_field',
				'label' => 'Код перенаправления',
				'default_val' => '301'
			]
		];
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		$this->before($vals);
		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		$this->before($vals);
		return parent::beforeUpdate($vals);
	}

	public function before(&$vals) {
		$vals['from'] = trim($vals['from']);
		$vals['to'] = trim($vals['to']);
		$vals['code'] = (int) $vals['code'];
	}

	public function checkByUrl() {
		$url = app()->request()->getRequestUri();
		$this->reader()
				->setWhere(['AND', '`from` = :from'], [':from' => $url])
				->objectByConds();
		if ($this->exists()) {
			app()->response()->redirect($this->val('to'), (int) $this->val('code'));
			exit();
		} else {
			$redirect_url = [];
			if (str()->pos($url, '/group/') !== false) {
				preg_match('~city=([0-9]+)~', $url, $city);
				preg_match('~([0-9]+)\.html~', $url, $group);
				if (isset($city[1]) && $city[1]) {
					$redirect_url[] = $city[1];
				} else {
					$redirect_url[] = '76004';
				}
				if (isset($group[1]) && $group[1]) {
					$redirect_url[] = 'catalog';
					$redirect_url[] = $group[1];
				}
				if ($redirect_url) {
					app()->response()->redirect('/' . implode('/', $redirect_url) . '/', 301);
					exit();
				}
			} elseif (str()->pos($url, '/subgroup/') !== false || str()->pos($url, '/catalog/sgr/') !== false) {
				preg_match('~city=([0-9]+)~', $url, $city);
				preg_match('~/([0-9]+)/catalog/~', $url, $city2);
				preg_match('~([0-9]+)\.html~', $url, $subgroup);
				preg_match('~/catalog/sgr/([0-9]+)/~', $url, $subgroup2);
				if (isset($city[1]) && $city[1]) {
					$redirect_url[] = $city[1];
				} elseif (isset($city2[1]) && $city2[1]) {
					$redirect_url[] = $city2[1];
				} else {
					$redirect_url[] = '76004';
				}
				if (isset($subgroup[1]) && $subgroup[1]) {
					$stss = new StsSubgroup();
					$stss->reader()
							->setSelect('id_group')
							->setWhere(['AND', 'id_subgroup = :id_subgroup'], [':id_subgroup' => $subgroup[1]])
							->objectByConds();

					if ($stss->val('id_group')) {
						$redirect_url[] = 'catalog';
						$redirect_url[] = $stss->val('id_group');
						$redirect_url[] = $subgroup[1];
					}
				} elseif (isset($subgroup2[1]) && $subgroup2[1]) {
					$stss = new StsSubgroup();
					$stss->reader()
							->setSelect('id_group')
							->setWhere(['AND', 'id_subgroup = :id_subgroup'], [':id_subgroup' => $subgroup2[1]])
							->objectByConds();
					if ($stss->val('id_group')) {
						$redirect_url[] = 'catalog';
						$redirect_url[] = $stss->val('id_group');
						$redirect_url[] = $subgroup2[1];
					}
				}
				if ($redirect_url) {
					app()->response()->redirect('/' . implode('/', $redirect_url) . '/', 301);
					exit();
				}
			} elseif (str()->pos($url, '/firmgroup/') !== false || str()->pos($url, '/firmsubgroup/') !== false) {
				preg_match('~city=([0-9]+)~', $url, $city);
				if (isset($city[1]) && $city[1]) {
					$redirect_url[] = $city[1];
				} else {
					$redirect_url[] = '76004';
				}
				if ($redirect_url) {
					app()->response()->redirect('/' . implode('/', $redirect_url) . '/firm/catalog/', 301);
					exit();
				}
			} elseif (str()->pos($url, '/price/byfirm/') !== false) {
				preg_match('~byfirm/([0-9]+)/([0-9]+)~', $url, $ids);
				if (isset($ids[1]) && $ids[1] && isset($ids[2]) && $ids[2]) {
					$redirect_url[] = 'firm/show';
					$redirect_url[] = $ids[2];
					$redirect_url[] = $ids[1];
				}
				if ($redirect_url) {
					app()->response()->redirect('/' . implode('/', $redirect_url) . '/?mode=price', 301);
					exit();
				}
			} elseif (str()->pos($url, '/index.php') !== false) {
				app()->response()->redirect('/', 301);
				exit();
			}
		}
	}

	public function title() {
		return 'Правило';
	}

}
