<?php

namespace App\Action;

use App\Classes\Action;
use App\Model\FirmPromo as FirmPromoModel;
use App\Model\FirmPromoCatalog;
use App\Model\PriceCatalog;
use App\Presenter\FirmPromoItems;
use Sky4\Exception;
use Sky4\Model\Utils;
use function app;
use function str;

class FirmPromo extends Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new FirmPromoModel());
	}

	public function execute() {
		$params = app()->request()->processGetParams([
			'id_catalog' => ['type' => 'int']
		]);

		$title = 'Текущие акции, скидки, распродажи ' . app()->location()->currentName('genitive');
		if ($params['id_catalog'] !== null) {
			$catalog = new PriceCatalog();
			$catalog->get($params['id_catalog']);
			$path = $catalog->adjacencyListComponent()->getPath();

			app()->breadCrumbs()
					->setElem('Каталог фирм ' . app()->location()->currentName('genitive'), app()->link('/firm/catalog/'))
					->setElem($title, app()->link('/firm-promo/'));

			foreach ($path as $cat) {
				app()->breadCrumbs()
						->setElem('Акции и скидки в рубрике "' . $cat->name() . '"', app()->link(app()->linkFilter('/firm-promo/', ['id_catalog' => $cat->id()])));
			}

			$title = 'Акции и скидки в рубрике &quot;' . $catalog->name() . '&quot;';
			$text = $this->text()->getByLink('promo/common');
			if ($text->exists()) {
				//$text->setVal('text', str()->replace($text->val('text'), ['_Cn_', '_Cp_', '_Cg_', '_L_', '_Ci_'], [$catalog->name(), app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]));
				$text->setVal('metadata_key_words', str()->replace($text->val('metadata_key_words'), ['_Cn_', '_Cp_', '_Cg_', '_L_', '_Ci_'], [$catalog->name(), app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]));
				$text->setVal('metadata_description', str()->replace($text->val('metadata_description'), ['_Cn_', '_Cp_', '_Cg_', '_L_', '_Ci_'], [$catalog->name(), app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]));
				$text->setVal('metadata_title', str()->replace($text->val('metadata_title'), ['_Cn_', '_Cp_', '_Cg_', '_L_', '_Ci_'], [$catalog->name(), app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]));
				app()->metadata()->setFromModel($text);
			} else {
				app()->metadata()->setTitle($title . app()->location()->currentName('prepositional'))->setHeader($title);
			}
		} else {
			$title = 'Текущие акции, скидки, распродажи ' . app()->location()->currentName('genitive');

			app()->breadCrumbs()
					->setElem('Каталог фирм ' . app()->location()->currentName('genitive'), app()->link('/firm/catalog/'))
					->setElem($title, app()->link('/firm-promo/'));
			app()->metadata()->setTitle($title)->setHeader($title)->setMetatag('description', $title)->setMetatag('keywords', $title);
		}

		//
		$presenter = new FirmPromoItems();
		$presenter->find($params);
		if (!$presenter->getItems()) {
			app()->metadata()->noIndex();
		}

		app()->frontController()->layout('catalog');
		$tags = $this->getTags($params);

		return $this->view()
						->set('advert_restrictions', app()->adv()->renderRestrictions())
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('filters', $params)
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render())
						->set('tags', $tags)
						->setTemplate('index')
						->save();
	}

	protected function getTags($params) {
		$result = [];
		$catalogs = [];
		$fp = new FirmPromoModel();
		$active_promo_ids = $fp->getActivePromoIds();
		if (!$active_promo_ids) return $result;

		if ($params['id_catalog'] === null) {
			$fp = new FirmPromoCatalog();
			$fp_conds = Utils::prepareWhereCondsFromArray($active_promo_ids, 'firm_promo_id');
			$rows = $fp->reader()
					->setWhere($fp_conds['where'], $fp_conds['params'])
					->rows();

			$ids = [];
			foreach ($rows as $row) {
				$ids[$row['price_catalog_id']] = 1;
			}

			$cat_ids = array_keys($ids);
			$catalog = new PriceCatalog();
			$cat_conds = Utils::prepareWhereCondsFromArray($cat_ids, 'id');
			$parent_ids = $catalog->reader()
					->setSelect(['parent_node'])
					->setWhere($cat_conds['where'], $cat_conds['params'])
					->rowsWithKey('parent_node');

			$cat_conds = Utils::prepareWhereCondsFromArray(array_keys($parent_ids), 'id');
			$catalogs = $catalog->reader()
					->setWhere($cat_conds['where'], $cat_conds['params'])
					->setOrderBy('web_many_name ASC')
					->objects();
		} else {
			$catalog = new PriceCatalog($params['id_catalog']);
			$childs = $catalog->adjacencyListComponent()->getChildren();
			$id_catalogs = [];
			foreach ($childs as $child) {
				$id_catalogs[] = $child->id();
			}
			if (!$id_catalogs) throw new Exception(Exception::TYPE_BAD_URL);

			$fp = new FirmPromoCatalog();
			$fp_conds = Utils::prepareWhereCondsFromArray($id_catalogs, 'price_catalog_id');
			$fp_conds_active = Utils::prepareWhereCondsFromArray($active_promo_ids, 'firm_promo_id');
			$rows = $fp->reader()
					->setWhere(['AND', $fp_conds['where'], $fp_conds_active['where']], $fp_conds['params'] + $fp_conds_active['params'])
					->rows();

			$ids = [];
			foreach ($rows as $row) {
				$ids[$row['price_catalog_id']] = 1;
			}

			if ($ids) {
				$cat_conds = Utils::prepareWhereCondsFromArray(array_keys($ids), 'id');
				$catalogs = $catalog->reader()
						->setWhere($cat_conds['where'], $cat_conds['params'])
						->setOrderBy('web_many_name ASC')
						->objects();
			}
		}

		foreach ($catalogs as $cat) {
			$result[$cat->id()] = [
				'catalog' => $cat,
				'count' => 0
			];
		}

		return $result;
	}
    
    protected function renderFirmPromoRubrics() {
        $this->view()
                ->setTemplate('firm_promo_rubrics');

        return $this->view()->render();
	}

}
