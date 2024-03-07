<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\Firm as FirmModel;
use App\Model\PriceCatalog;
use App\Model\StsPrice;
use App\Presenter\FirmItems;
use CObject;
use Sky4\Model\Utils;

class Firm extends Controller {

	public function actionNew() {
        if (str()->sub(app()->request()->getRequestUri(), -1) !== '/' && !isset($_GET['mode'])) {
            app()->response()->redirect(app()->link('/firm/new/'), 301);
        }
        
        app()->metadata()->setCanonicalUrl(app()->link('/firm/new/'));
		$this->actionDefaultList('/firm/new/', 'Новые фирмы', 'findNew');
	}

	public function actionPopular() {
        if (str()->sub(app()->request()->getRequestUri(), -1) !== '/' && !isset($_GET['mode'])) {
            app()->response()->redirect(app()->link('/firm/popular/'), 301);
        }

        app()->metadata()->setCanonicalUrl(app()->link('/firm/popular/'));
		$this->actionDefaultList('/firm/popular/', 'Популярные фирмы', 'findPopular');
	}

	public function actionBest() {
        if (str()->sub(app()->request()->getRequestUri(), -1) !== '/' && !isset($_GET['mode'])) {
            app()->response()->redirect(app()->link('/firm/best/'), 301);
        }

        app()->metadata()->setCanonicalUrl(app()->link('/firm/best/'));
		$this->actionDefaultList('/firm/best/', 'Лидеры рейтинга фирм', 'findBest');
	}

	public function actionDefaultList($link, $header, $presenter_method_name) {
		app()->breadCrumbs()
				->setElem('Каталог фирм', app()->link('/firm/catalog/'))
				->setElem($header, app()->link($link));

		$filters = app()->request()->processGetParams([
			'mode' => ['type' => 'string']
		]);

		$presenter = new FirmItems();

		if ($filters['mode'] === 'map') {
			$presenter->setLimit(1000)
					->setPage($this->getPage());
		} else {
			$presenter->setLimit(app()->config()->get('app.firms.onpage', 20))
					->setPage($this->getPage());
		}

		$presenter->pagination()
				->setLink(app()->link($link));

		CObject::execute($presenter, $presenter_method_name, [$filters]);

		$this->text()->getByLink($link);
		app()->metadata()
				->setFromModel($this->text(), true)
				->setHeader($header . ' ' . app()->location()->currentCaseName('genitive'));

		if ($filters['mode'] === 'map') {
			app()->setUseMap(true);
		}

		app()->tabs()->setTabs([
					['link' => app()->link($link), 'label' => 'Компании'],
					['link' => app()->link(app()->linkFilter($link, $filters, ['mode' => 'map'])), 'label' => 'На карте', 'nofollow' => true]
				])
				->setTabsNumericValues([null, null])
				->setLink(app()->linkFilter($link))
				->setActiveTab($filters['mode'] === 'map' ? 1 : 0);

		app()->frontController()->layout()->setTemplate('catalog');

		$this->view()
				->set('has_results', count($presenter->getItems()) > 0)
				->set('items', $presenter->renderItems())
				->set('url', app()->linkFilter($link, $filters))
				->set('header', app()->metadata()->getHeader())
				->set('pagination', $presenter->pagination()->render())
				->set('pager', $presenter->pagination())
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('filters', $filters)
				->set('text', str()->replace($this->text()->val('text'), ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]))
				->set('tabs', app()->tabs()->render())
				->setTemplate('default_list', 'firm');
	}

	public function actionVideos() {
		app()->response()->redirect(app()->link('/firm-video/'));
	}

	public static function renderTagsByFirm(FirmModel $firm, $filters = [], $template = 'firm.catalog_tags_chunk') {
		$items = [];
		$id_catalog = (isset($filters['id_catalog']) && $filters['id_catalog'] !== null) ? (int) $filters['id_catalog'] : null;

		$pcp = new \App\Model\PriceCatalogPrice();
		if ($id_catalog !== null) {
			$catalog = new PriceCatalog($id_catalog);
			$pcp = new \App\Model\PriceCatalogPrice();
			$items = $pcp->getCatalogTagsByFirm($firm, $catalog);

			$result = app()->chunk()
					->set('items', $items)
					->set('link', app()->uri(), $filters)
					->set('filter', $filters)
					->set('groups', [])
					->render($template !== 'firm.catalog_tags_chunk' ? $template : 'common.catalog_tags');
		} else {
			list($items, $groups, $subgroups) = $pcp->getCatalogTagsByFirm($firm);

            $result = app()->chunk()
					->set('firm', $firm)
					->set('items', $items)
					->set('groups', $groups)
					->set('subgroups', $subgroups)
					->render($template);
		}

		return $result;
	}

	public static function getSortingOptions() {
		return [
			'default' => [
				'field' => 'rating',
				'direction' => 'desc',
				'name' => 'по рейтингу'
			],
			'alpha' => [
				'field' => 'company_name',
				'direction' => 'asc',
				'name' => 'по алфавиту'
			]
		];
	}

}
