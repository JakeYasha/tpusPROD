<?php

namespace App\Action\Firm;

use App\Model\FirmFirmType;
use App\Model\FirmType;
use App\Presenter\FirmItems;
use Sky4\Exception;
use Sky4\Model\Utils;
use App\Model\AdvText;

class Bytype extends \App\Action\Firm {

	public function execute($id_type = null, $id_sub_type = null, $garbage = null) {
		if (!isset($id_type) || !is_numeric($id_type)) {
			throw new Exception();
		}

		$firm_type = new FirmType($id_type);
		$this->bytypeRedirect($id_type, $id_sub_type, $firm_type, $garbage);

		$filters = app()->request()->processGetParams([
			'id_district' => ['type' => 'int'],
			'sorting' => ['type' => 'string'],
			'on_map' => ['type' => 'int'],
			'page' => ['type' => 'int']
		]);

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		app()->breadCrumbs()
				->setElem('Каталог фирм', app()->link('/firm/catalog/'))
				->setElem($firm_type->val('name'), app()->link('/firm/bytype/' . $firm_type->id() . '/'));

		$presenter = new FirmItems();
		
		app()->setVar('on_map_link', app()->linkFilter('/firm/bytype/' . implode('/', func_get_args()) . '/', $filters, ['on_map' => 1]));

		if ($filters['on_map']) {
			$presenter->setLimit(1000)
					->setPage($this->getPage());
		} else {
			$presenter->setLimit(app()->config()->get('app.firms.onpage', 20))
					->setPage($this->getPage());
		}


		if ($id_sub_type === null) {
			$presenter->pagination()
					->setLinkParams($no_page_filters)
					->setBasicLink(app()->link('/firm/bytype/' . $id_type . '/'))
					->setLink(app()->link('/firm/bytype/' . $id_type . '/'));

			$presenter->findByType($id_type, null, $filters);

			$rel_model = new FirmFirmType();
			$cities_where_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
			$firm_sub_types = $firm_type->reader()
					->setWhere(['AND', '`parent_node` = :id_type'], [':id_type' => $id_type])
					->setOrderBy("`name` ASC")
					->objects();
			$types_where_conds = Utils::prepareWhereCondsFromArray(array_keys($firm_sub_types), 'id_type');
			$_where = [
				'AND',
				$types_where_conds['where'],
				$cities_where_conds['where']
			];

			$_params = array_merge($cities_where_conds['params'], $types_where_conds['params']);

			$sub_items_ids = array_keys($rel_model->reader()
							->setWhere($_where, $_params)
							->rowsWithKey('id_type'));

			$sub_items_ids_conds = Utils::prepareWhereCondsFromArray($sub_items_ids);
			$sub_items = $firm_type->reader()
					->setSelect(['id', 'name', 'count', 'parent_node'])
					->setWhere(['AND', '`parent_node` = :parent', $sub_items_ids_conds['where']], array_merge([':parent' => $firm_type->id()], $sub_items_ids_conds['params']))
					->setOrderBy('`name` ASC')
					->objects();
		} else {
			$firm_type = new FirmType($id_sub_type);
			$parent_type = new FirmType($id_type);

			app()->breadCrumbs()
					->setElem($firm_type->val('name'), app()->link('/firm/bytype/' . $firm_type->id() . '/'));
			$presenter->pagination()
					->setLinkParams($no_page_filters)
					->setBasicLink(app()->link('/firm/bytype/' . $id_type . '/' . $id_sub_type . '/'))
					->setLink(app()->link('/firm/bytype/' . $id_type . '/' . $id_sub_type . '/'));

			$presenter->findByType($id_type, $id_sub_type, $filters);

			$sub_items = [];
		}

		foreach ($filters as $val) {
			if ($val !== null) {
				app()->metadata()->setCanonicalUrl(app()->link('/firm/bytype/' . ($firm_type->val('parent_node') ? ($firm_type->val('parent_node') . '/' . $firm_type->id()) . '/' : ($firm_type->id()) . '/')));
				break;
			}
		}

		if ($filters['on_map']) {
			app()->setUseMap(true);
			app()->metadata()->setDefault($firm_type->name() . '_Cp_ - компании на карте _Cg_', $firm_type->name() . ', _Ci_, адреса, телефоны, сайты, компании на карте', 'Компании по направлению деятельности - ' . $firm_type->name() . ' на карте _Cg_.', true, $presenter->pagination(), $filters);
		} else {
			if (app()->location()->city()->exists()) {
				app()->metadata()->set($firm_type, $firm_type->name() . '_Cp_ - адреса, телефоны, сайты компаний', $firm_type->name() . ', _Ci_, адреса, телефоны, сайты, компании на карте', 'Список компаний _Cg_ по направлению деятельности - ' . $firm_type->name() . '. Контактная информация, адреса на карте города, официальные сайты', true, $presenter->pagination(), $filters, true);
			} elseif (app()->location()->region()->exists()) {
				app()->metadata()->set($firm_type, $firm_type->name() . ' - адреса, телефоны, сайты компаний региона _Ci_', $firm_type->name() . ', _Ci_, адреса, телефоны, сайты, компании на карте', 'Компании региона _Ci_ по направлению деятельности - ' . $firm_type->name() . '. Контактная информация, адреса на карте, официальные сайты', true, $presenter->pagination(), $filters, true);
			}
			app()->tabs()
					->setSortOptions(self::getSortingOptions())
					->setActiveSortOption($filters['sorting'] === '' ? 'default' : $filters['sorting']);
		}

		if ($filters['sorting'] || $filters['on_map'] || !app()->location()->city()->exists()) {
			app()->metadata()->noIndex();
		}

		$firm_type = [
			'id' => $firm_type->id(),
			'name' => str()->replace($firm_type->val('name'), ['_Cp_', '_Cg_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentName()]),
			'text' => $firm_type->val('text') ? str()->replace($firm_type->val('text'), ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentFullId(), app()->location()->currentName()]) : '',
			'text_bottom' => str()->replace($firm_type->val('text_bottom'), ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]),
			'parent_node' => $firm_type->val('parent_node'),
			'advert_restrictions' => $firm_type->val('advert_restrictions'),
            'case' => $firm_type->val('case'),
		];
        
        $firm_type['title'] = $firm_type['name'] . 
            (' ' . (app()->location()->city()->exists() 
                ? app()->location()->currentName($firm_type['case'] == 1 ? 'genitive' : 'prepositional') 
                : app()->location()->region()->exists() ?
                    app()->location()->currentName('prepositional')
                    : ''));

		app()->tabs()->setTabs([
					['link' => app()->link('/firm/bytype/' . ($firm_type['parent_node'] ? ($firm_type['parent_node'] . '/' . $firm_type['id']) . '/' : ($firm_type['id']) . '/')), 'label' => 'Компании'],
					['link' => app()->link('/firm/bytype/' . ($firm_type['parent_node'] ? ($firm_type['parent_node'] . '/' . $firm_type['id']) . '/' : ($firm_type['id']) . '/') . '?on_map=1'), 'label' => 'На карте', 'nofollow' => true]
				])
				->setTabsNumericValues([$presenter->pagination()->getTotalRecords(), null])
				//->setLink(app()->link(app()->linkFilter('/firm/bytype/' . implode('/', func_get_args()) . '/', $filters)))
				->setActiveTab($filters['on_map'] ? 1 : 0);

        if (app()->isNewTheme()) {
            $cc = new \App\Controller\Catalog();
            app()->frontController()->layout()
                    ->setVar('rubrics', $cc->renderRubrics(null, true))
                    ->setVar('mobile_rubrics', $cc->renderRubrics(null, true, 'default', 'mobile_rubrics'))
                    ->setTemplate('catalog');
        } else {
    		app()->frontController()->layout()->setTemplate('catalog');
        }
        
		//$keywords = explode('[, ]', trim($firm_type['name']));
        $keywords = preg_split("/[\s,]+/", trim($firm_type['name']));

        foreach ($keywords as $key) {
			app()->adv()
					->addKeyword(trim($key));
		}
		app()->adv()->setAdvertRestrictions($firm_type['advert_restrictions']);
                $adv_text = new AdvText();
		$this->view()
				->set('advert_restrictions', app()->adv()->renderRestrictions())
				->set('items', $presenter->renderItems())
				->set('url', app()->linkFilter('/firm/bytype/' . implode('/', func_get_args()) . '/', $filters))
				->set('header', str()->replace(app()->config()->get('app.firm-type.default-text'), ['_Cp_', '_Cg_', '_Title_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), $firm_type['name']]))
				->set('pagination', $presenter->pagination()->render())
				->set('pager', $presenter->pagination())
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('firm_type', $firm_type)
				->set('filters', $filters)
				->set('sub_items', $sub_items)
                                ->set('position', $adv_text->getByUrl(app()->location()->linkPrefix() . app()->request()->getRequestUri()))
				->set('tabs', app()->tabs()->render())
                ->set('title', $firm_type['title'])
				->setTemplate('bytype')
				->save();
	}

}
