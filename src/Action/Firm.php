<?php

namespace App\Action;

use App\Classes\Action;
use App\Controller\Catalog;
use App\Controller\Firm as FirmController;
use App\Controller\FirmPromo;
use App\Model\CurrentRegionCity;
use App\Model\Firm as FirmModel;
use App\Model\FirmFirmType;
use App\Model\FirmReview;
use App\Model\FirmType;
use App\Model\FirmTypeCity;
use App\Model\FirmVideo;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogCount;
use App\Model\StatObject;
use App\Presenter\FirmItems;
use App\Presenter\PriceItems;
use CDbConnection;
use Sky4\Exception;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;
use Sky4\Widget\InterfaceElem\Autocomplete;
use function app;
use function encode;
use function html;
use function str;

class Firm extends Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new FirmModel());
	}

	public function execute() {
		if ($this->args && isset($this->args[0])) {
			$ctr = new FirmController();
			switch ($this->args[0]) {
				case 'best' :
					$ctr->actionBest();
					break;
				case 'new' :
					$ctr->actionNew();
					break;
				case 'popular' :
					$ctr->actionPopular();
					break;
				default : throw new Exception();
			}

			$this->view = $ctr->view();
			$this->view()->save();
		} else {
			throw new Exception();
		}
	}

	protected function bytypeRedirect($id_type, $id_sub_type, FirmType $parent, $garbage) {
        $filters = app()->request()->processGetParams([
			'on_map' => ['type' => 'int'],
			'sorting' => ['type' => 'string'],
		]);
        
        if (str()->sub(app()->request()->getRequestUri(), -1) !== '/' && !isset($filters['sorting']) && !isset($filters['on_map'])) {
            app()->response()->redirect(app()->link(app()->request()->getRequestUri() . '/'), 301);
        }
        
		if ($id_sub_type === '0') app()->response()->redirect(app()->link('/firm/bytype/'.$id_type.'/'), 301);
		if ($garbage !== null) app()->response()->redirect(app()->link('/firm/bytype/'.$id_type.'/'.$id_sub_type.'/'), 301);
		if ((int)$id_sub_type !== 0 && $parent->exists()) {
			$firm_sub_type = new FirmType($id_sub_type);
			if ($firm_sub_type->val('parent_node') !== $parent->id()) {
				$parent = new FirmType($firm_sub_type->val('parent_node'));
				app()->response()->redirect(app()->link('/firm/bytype/'.$parent->id().'/'.(int)$id_sub_type.'/'), 301);
			}
			if (strpos(app()->url(), '/firm/bytype/'.$parent->id().'/'.(int)$id_sub_type.'/') === false) {
				app()->response()
						->redirect(app()->link(str_replace(
												'/firm/bytype/'.$parent->id().'/'.(int)$id_sub_type, '/firm/bytype/'.$parent->id().'/'.(int)$id_sub_type.'/', app()->url()
								)), 301);
			}
		} else if ((int)$id_sub_type !== 0) {
			$firm_sub_type = new FirmType($id_sub_type);
			$parent = new FirmType($firm_sub_type->val('parent_node'));
			app()->response()->redirect(app()->link('/firm/bytype/'.$parent->id().'/'.(int)$id_sub_type.'/'), 301);
		}
	}

	protected function cacheControl() {
		/* app()->response()
		  ->setLastModified(strtotime($this->model()->val('timestamp_last_updating')))
		  ->setExpires(mktime(7, 0, 0, date("m"), date("d") + 1))
		  ->setCacheControl('public');

		  if (app()->request()->isNotModifiedSince(strtotime($this->model()->val('timestamp_last_updating')))) {
		  app()->response()->code304();
		  } */
	}

	/**
	 * 
	 * @return FirmModel
	 */
	public function model() {
		return parent::model();
	}

	public function findModelObject($id_firm, $id_service = null) {
		if ($id_service === null) {
			$this->model()->reader()
					->setWhere(['AND', '`id_firm` = :firm', 'flag_is_active = :flag_is_active'], [':firm' => $id_firm, ':flag_is_active' => 1])
					->objectByConds();
		} else {
			$this->model()->reader()
					->setWhere(['AND', '`id_firm` = :firm', '`id_service` = :service', ['OR', 'flag_is_active = :flag_is_active', ['AND', 'flag_is_active = :flag_is_not_active', 'timestamp_ratiss_updating > :timestamp_ratiss_updating']]], [':firm' => $id_firm, ':service' => $id_service, ':flag_is_active' => 1, ':flag_is_not_active' => 0, 'timestamp_ratiss_updating' => DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m') - 6))])
					->objectByConds();
		}


		if (!$this->model()->exists()) {
			throw new Exception(Exception::TYPE_BAD_URL); //Фирма заблокирована (совсем)
		}
		return $this;
	}

	protected function setTabs($filters) {
        if (APP_IS_DEV_MODE) {
            $tabs = [
                ['link' => app()->linkFilter(parse_url(app()->uri())['path'], ['mode' => null], []), 'label' => 'О фирме', 'display' => true],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'price']), 'label' => 'Прайс-лист', 'disabled' => !$this->model()->hasPriceList(), 'nofollow' => true],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'video']), 'label' => 'Видеоблог', 'disabled' => !$this->model()->hasVideo()],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'review']), 'label' => 'Отзывы', 'disabled' => !$this->model()->hasReviews()],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'promo']), 'label' => 'Акции', 'disabled' => !$this->model()->hasPromo()],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'firm-branch']), 'label' => 'Другие адреса', 'disabled' => !$this->model()->hasFirmBranches()]
            ];
        } else {
            $tabs = [
                ['link' => app()->linkFilter(parse_url(app()->uri())['path'], ['mode' => null], []), 'label' => 'Общая информация', 'display' => true],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'price']), 'label' => 'Прайс-лист', 'display' => $this->model()->hasPriceList(), 'nofollow' => true],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'video']), 'label' => 'Видеоблог', 'display' => $this->model()->hasVideo()],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'review']), 'label' => 'Отзывы', 'display' => $this->model()->hasReviews()],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'promo']), 'label' => 'Акции', 'display' => $this->model()->hasPromo()],
                ['link' => app()->linkFilter(app()->uri(), ['mode' => 'firm-branch']), 'label' => 'Другие адреса', 'display' => $this->model()->hasFirmBranches()]
            ];
        }

		app()->tabs()
				->setAdditionalWrapper('firm-search-menu')
				->setTabs($tabs);
	}

	protected function checkUrl($rule, $redirect) {
		if (!preg_match('~'.$rule.'~', app()->url())) {
			app()->response()->redirect($redirect, 301);
			exit();
		}
	}

	protected function catalog() {
		app()->breadCrumbs()
				->setElem('Каталог фирм', app()->link('/firm/catalog/'));

		if (app()->location()->city()->exists()) {
			$header = 'Каталог фирм и организаций'.app()->location()->currentCaseName('genitive');
			app()->metadata()->setTitle('Каталог фирм и организаций'.app()->location()->currentCaseName('genitive').' - TovaryPlus.ru')
					->setMetatag('keywords', 'каталог фирм, все фирмы города '.app()->location()->currentName().', производственные, строительные, торговые, отраслевые компании'.app()->location()->currentCaseName('genitive').', организации'.app()->location()->currentCaseName('genitive').', поставщики, производители, предприятия сферы услуг')
					->setMetatag('description', 'На страницах каталога размещены рекламные и справочно-информационные материалы о частных, муниципальных и государственных организациях и компаниях '.app()->location()->currentCaseName('genitive').' сгруппированные по направлениям деятельности, адреса, телефоны, прайс-листы фирм, сайты, видео материалы компаний.');
		} elseif (app()->location()->region()->exists()) {
			$header = 'Каталог фирм и организаций региона '.app()->location()->currentName();
			app()->metadata()->setTitle('Каталог фирм и организаций региона '.app()->location()->currentName().' - TovaryPlus.ru')
					->setMetatag('keywords', 'каталог фирм, все фирмы региона '.app()->location()->currentName().', производственные, строительные, торговые, отраслевые компании региона '.app()->location()->currentName().', организации региона '.app()->location()->currentName().', поставщики, производители, предприятия сферы услуг')
					->setMetatag('description', 'На страницах каталога размещены рекламные и справочно-информационные материалы о частных, муниципальных и государственных организациях и компаниях региона '.app()->location()->currentName().' сгруппированные по направлениям деятельности, адреса, телефоны, прайс-листы фирм, сайты, видео материалы компаний.');
		}

		$ftc = new FirmTypeCity();
		$params = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city'); //@todo
		$ftc_items = $ftc->reader()->setWhere($params['where'], $params['params'])->rows();

		$types_ids = $main_types = [];
		$cities = [];
		foreach ($ftc_items as $ftc_item) {
			if (!isset($types_ids[$ftc_item['id_type']])) $types_ids[$ftc_item['id_type']] = 0;
			$types_ids[$ftc_item['id_type']] += $ftc_item['cnt'];
			$cities[$ftc_item['id_city']] = true;
		}

		$out_array = [];

		if (!app()->location()->city()->exists() && app()->location()->region()->exists()) {
			$model = new CurrentRegionCity();
			$cities_conds = Utils::prepareWhereCondsFromArray(array_keys($cities), 'id_city');
			$where = ['AND', '`id_country` = :id_country', $cities_conds['where']];
			$params = [':id_country' => app()->location()->country()->id()];
			$params = array_merge($params, $cities_conds['params']);

			$stat = $model->reader()
					->setWhere($where, $params)
					->setOrderBy('`count_firms` DESC')
					->objects();

			$stat_row = [];
			foreach ($stat as $row) {
				$stat_row[] = [
					'name' => str()->firstCharToUpper(str()->toLower($row->name())),
					'count_firms' => $row->val('count_firms'),
					'id_city' => $row->val('id_city')
				];
			}

			$this->view()->set('region_stat', $stat_row);
		} else {
			$this->view()->set('region_stat', null);
		}

		if ($types_ids) {
			$ft = new FirmType();
			$params = Utils::prepareWhereCondsFromArray(array_keys($types_ids), 'id');
			$params['where'] = ['AND', $params['where'], '`parent_node` != :0', '`node_level` != :1'];
			$params['params'][':0'] = 0;
			$params['params'][':1'] = 1;
			$ft_items = $ft->reader()
					->setSelect(['id', 'name', 'parent_node'])
					->setWhere($params['where'], $params['params'])
					->setOrderBy('`name` ASC')
					->rowsWithKey('id');

			$parents = [];
			foreach ($ft_items as $tp) {
				$parents[] = $tp['parent_node'];
			}

			$parents = array_unique($parents);
			$params1 = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
			$params2 = Utils::prepareWhereCondsFromArray($parents, 'id_type');
			$main_count = $ftc->reader()->setWhere(['AND', $params1['where'], $params2['where']], array_merge($params1['params'], $params2['params']))->rowsWithKey('id_type');

			$result = [];
			foreach ($ft_items as $tp) {
				if (!isset($result[$tp['parent_node']])) $result[$tp['parent_node']] = ['cnt' => isset($main_count[$tp['parent_node']]['cnt']) ? $main_count[$tp['parent_node']]['cnt'] : 0, 'childs' => []];
				$result[$tp['parent_node']]['childs'][] = $tp;
			}

			$params = Utils::prepareWhereCondsFromArray(array_keys($result), 'id');
			$main_types = $ft->reader()->setSelect(['id', 'name', 'parent_node'])->setWhere($params['where'], $params['params'])->setOrderBy('`name` ASC')->rowsWithKey('id');

			foreach ($result as $k => $v) {
				$v['mtkey'] = $k;
				if (isset($main_types[$k]['name'])) $out_array[$main_types[$k]['name']] = $v;
			}

			ksort($out_array);
		}
        
		$cc = new Catalog();
        if (app()->isNewTheme()) {
            app()->frontController()->layout()
                    ->setVar('rubrics', $cc->renderRubrics(null, true))
                    ->setVar('mobile_rubrics', $cc->renderRubrics(null, true, 'default', 'mobile_rubrics'))
                    ->setTemplate('catalog');
        }

		$this->view()
				->set('rubrics', $cc->renderRubrics(null, true))
				->set('items', $out_array)
				->set('header', $header)
				->set('main_types', $main_types);

		if (!$out_array) app()->metadata()->noIndex();
	}

	protected function renderCommonInfoBlock($types = null, $branches = [], $branch_names = []) {
		app()->tabs()->setActiveTab(0);
		$price_presenter = new PriceItems();
		//$price_presenter->findPopularInFirm($this->model());

		$firm_on_map = '';
        $firm_branches_on_map = '';

		app()->tabs()->setSortOptions([]);

        app()->metadata()->setOgMetatag('og:url', APP_URL . app()->url());
        app()->metadata()->setOgMetatag('og:title', app()->metadata()->getTitle());
        app()->metadata()->setOgMetatag('og:description', app()->metadata()->getTitle().'. Адрес: '.$this->model()->shortAddress().', телефоны: '.$this->model()->phone());

        app()->metadata()
				->setMetatag('keywords', $this->model()->getCity().', '.app()->metadata()->getTitle().', адрес, телефоны, режим работы, официальный сайт, прайс-лист, отзывы')
				->setMetatag('description', app()->metadata()->getTitle().'. Адрес: '.$this->model()->shortAddress().', телефоны: '.$this->model()->phone())
				//$this->model()->modeWork() //режим работы
				//$this->model()->activity() //вид деятельности
				->setTitle(app()->metadata()->getTitle().', '.$this->model()->getCity().' (id#'.$this->model()->id_firm().($this->model()->isBranch() ? '/'.$this->model()->branch_id : '').')');

		//формируем добавочный блок подгрупп снизу витрины
		$pcp = new \App\Model\PriceCatalogPrice();
		$big_catalogs = $this->model()->hasPriceList() ? $pcp->getCatalogsByCount($this->model()) : [];

		$types = $types === null ? $this->model()->getTypes() : $types;

		$analogs = [];
		if ($types !== null && $this->model()->isBlocked()) {
			$fft = new FirmFirmType();
			$analogs = $fft->getAnalogsByTypes(array_keys($types), $this->model());
		}

		$advert_modules = '';
		if ($this->model()->priority() < 20 && ($_SERVER['REMOTE_ADDR'] == '93.158.228.86' || $_SERVER['REMOTE_ADDR'] == '93.181.225.108') && isset($_GET['debug_mode'])) {
			$advert_modules = app()->adv()->renderFirmAdvertModulesByFirm($this->model());
		}

		if ($this->model()->hasAddress()) {
			app()->setUseMap(true);
			$presenter = new FirmItems();
			$presenter
					->setLimit(1000)
					->setPage($this->getPage());

			$firms_on_map = [$this->model()->id() => $this->model()];
			if ($branches['branches']) {
				foreach ($branches['branches'] as $branch) {
					$firms_on_map[$branch->id()] = $branch;
				}
			}

			$presenter
					->setModel($this->model())
					->findByBranchesIds($firms_on_map, ['mode' => 'map'], false);

			$firm_on_map = $presenter->setItemsTemplate('bottom_block_common_info_map')->renderItems();
            
            //Филиалы фирмы
            $presenter = new FirmItems();
            $presenter
                    ->setLimit(1000)
                    ->setPage($this->getPage());

            $firm_branches_on_map = [$this->model()->id() => $this->model()];
            if ($branches['firm_branches']) {
                foreach ($branches['firm_branches'] as $id_city => $items) {
                    foreach ($items as $firm_branch) {
                        $firm_branches_on_map[$firm_branch->id()] = $firm_branch;
                    }
                }
            }

            $presenter
                    ->setModel($this->model())
                    ->findByFirmBranchesIds($firm_branches_on_map, ['mode' => 'map'], false);

            $firm_branches_on_map = $presenter->setItemsTemplate('bottom_block_common_info_map')
                    ->setParams('show_company_activity', 0)
                    ->renderItems();
		}

		return $this->view()
						->set('analogs', $analogs)
						->set('branches', $branches)
						->set('branch_names', $branch_names)
						->set('big_catalogs', $big_catalogs)
						->set('delivery', $this->model()->getDelivery())
						->set('gallery', $this->model()->getGallery())
						->set('popular_items', '')//$price_presenter->renderItems())
						->set('firm_on_map', $firm_on_map)
						->set('firm_branches_on_map', $firm_branches_on_map)
						->set('files', $this->model()->getFiles())
						->set('advert_modules', $advert_modules)
						->set('item', $this->model())
						->set('tabs', app()->tabs()->render())
						->set('types', $types)
						->set('reviews', $this->getReviews()['items'])
						->set('reviews_count', $this->getReviewsCount())
						->set('questions', $this->getQuestions())
						->set('videos', $this->getVideos(1))
						->setTemplate('bottom_block_common_info')
						->render();
	}

	protected function renderPriceListBlock() {
		app()->tabs()->setActiveTab(1);
		app()->stat()->addObject(StatObject::PRICE_LIST, $this->model());
		app()->breadCrumbs()->setElem('Прайс-лист', app()->linkFilter($this->model()->linkItem(), ['mode' => 'price']));

		$price_presenter = new PriceItems();
		$title_end = 'Прайс лист фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity();
		if ($this->params['id_catalog'] === null && $this->params['q'] === null) {
			$price_presenter->findPopularInFirm($this->model());
			app()->metadata()->set(new PriceCatalog(), $title_end, $title_end, 'Каталог предложений товаров и услуг фирмы '.app()->metadata()->getTitle().', цены, текущие акции и скидки');
		} else {
			$price_presenter->setLimit(12);
			$price_presenter->findAllInFirm($this->model(), $this->params);
			if ($this->params['q'] === null) {
				$catalog = new PriceCatalog($this->params['id_catalog']);
				app()->metadata()->set(new PriceCatalog(), $catalog->name().'. '.$title_end, $catalog->name().$title_end, $catalog->name().'. Каталог предложений товаров и услуг фирмы '.app()->metadata()->getTitle(), null, $price_presenter->pagination(), $this->params);
			} else {
				if ($this->params['q'] === '') {
					app()->metadata()->set(new PriceCatalog(), 'Полный список предложений. '.$title_end, 'Полный список предложений '.$title_end, 'Полный список предложений товаров и услуг в фирме '.app()->metadata()->getTitle().', '.$this->model()->getCity(), null, $price_presenter->pagination(), $this->params);
				} else {
					app()->metadata()->set(new PriceCatalog(), 'Поиск по запросу '.encode($this->params['q']).'. '.$title_end, 'Поиск по запросу '.encode($this->params['q']).'. '.$title_end, 'Поиск по запросу '.encode($this->params['q']).'.Каталог предложений товаров и услуг фирмы '.app()->metadata()->getTitle(), null, $price_presenter->pagination(), $this->params);
				}
			}
		}

		foreach ($this->params as $k => $val) {
			if ($val !== null && ($k === 'sorting' || $k === 'display_mode')) {
				if ($this->model()->sourceIsRatiss()) {
					app()->metadata()->setCanonicalUrl(app()->linkFilter('/firm/show/'.$this->model()->id_firm().'/'.$this->model()->id_service().'/', $this->params, ['sorting' => false, 'display_mode' => false]));
				} else {
					app()->metadata()->setCanonicalUrl(app()->linkFilter('/firm/show/'.$this->model()->id().'/', $this->params, ['sorting' => false, 'display_mode' => false]));
				}

				break;
			}
		}

		$autocomplete = new Autocomplete();
		$attrs = [
            'id' => 'price-search-autocomplete', 
            'placeholder' => 'Поиск по прайс-листу фирмы',
            'class' => 'form__control form__control--search-input'
        ];
		if ($this->params['q'] !== null) {
			$attrs['value'] = (string)urldecode($this->params['q']);
		}
		$autocomplete
				->setName('q')
				->setAttrs($attrs)
				->setParams([
					'model_alias' => 'price-search',
					'val_mode' => 'id',
					'field_name' => $this->model()->id()
		]);
        
        $tags = $this->params['id_catalog'] !== null ? FirmController::renderTagsByFirm($this->model(), $this->params, 'firm.catalog_tags_chunk_with_no_follow') : FirmController::renderTagsByFirm($this->model(), $this->params);

		return $this->view()
						->set('advert_restrictions', app()->adv()->renderRestrictions())
						->set('advert_age_restrictions', app()->adv()->renderAgeRestrictions())
						->set('autocomplete', $autocomplete->render())
						->set('item', $this->model())
						->set('files', $this->model()->getFiles())
						->set('items', $price_presenter->renderItems())
						->set('pagination', $price_presenter->pagination()->render())
						->set('total_founded', $price_presenter->pagination()->getTotalRecords())
						->set('total_price_list_count', $this->model()->getTotalPriceListCount())
						->set('tabs', app()->tabs()->render(true))
						->set('tags', $tags)
						->set('inside_bread_crumbs', app()->breadCrumbs()->renderBottom())
						->set('sorting', app()->tabs()
								->setActiveSortOption($this->params['sorting'])
								->renderSorting())
						->set('filters', $this->params)
						->setTemplate('bottom_block_price_list')
						->render();
	}

	protected function renderVideoBlock() {
		$filters = app()->request()->processGetParams(['id' => 'int']);

		app()->tabs()->setActiveTab(2)->setSortOptions([]);
		app()->breadCrumbs()->setElem('Видеоблог', app()->linkFilter($this->model()->linkItem(), ['mode' => 'video']));
		if ($filters['id'] === null) {
			app()->stat()->addObject(StatObject::VIDEO_LIST, $this->model());
			app()->metadata()
					->setMetatag('keywords', 'Подборка видеороликов, видеоблог фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity())
					->setMetatag('description', 'Подборка видеороликов о фирме '.app()->metadata()->getTitle().' ('.$this->model()->getCity().'), предлагаемых ей товарах и услугах.')
					->setTitle('Видеоблог фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity());

			$this->view()
					->set('items', $this->getVideos())
					->setTemplate('bottom_block_video');
		} else {
			$item = new FirmVideo($filters['id']);
			$item->setVal('video_code', html_entity_decode($item->val('video_code')));
			if (!$item->exists()) {
				throw new Exception();
			}
			app()->stat()->addObject(StatObject::VIDEO_SHOW, $item);
			app()->breadCrumbs()->setElem($item->name(), '');
			app()->metadata()
					->setMetatag('keywords', 'видеоролик '.$item->name().' от фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity())
					->setMetatag('description', html()->strip($item->val('text')).' - еще больше информации на страницах видеоблога')
					->setTitle($item->name().', видеоролик фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity());

			$this->view()
					->set('item', $item)
					->set('items', $this->getVideos(10, $item->id()))
					->set('reviews', $this->getReviews()['items'])
					->set('reviews_count', $this->getReviewsCount())
					->setTemplate('show_video');
		}

		return $this->view()
						->set('firm', $this->model())
						->set('tabs', app()->tabs()->render())
						->render();
	}

	protected function renderReviewBlock() {
		$ajax = app()->request()->processGetParams(['ajax' => 'int'])['ajax'];
		if ($ajax === null) {
			app()->tabs()->setActiveTab(3)->setSortOptions([]);
			app()->stat()->addObject(StatObject::REVIEW_LIST, $this->model());
			app()->breadCrumbs()->setElem('Отзывы', app()->linkFilter($this->model()->linkItem(), ['mode' => 'review']));
			app()->metadata()
					->setMetatag('keywords', 'Отзывы о фирме '.app()->metadata()->getTitle().', '.$this->model()->getCity().', добавить отзыв')
					->setMetatag('description', 'Читайте отзывы и оставляйте свои рассказывающие о фирме '.app()->metadata()->getTitle().' ('.$this->model()->getCity().'), предлагаемых товарах и услугах.')
					->setTitle('Отзывы о фирме '.app()->metadata()->getTitle().', '.$this->model()->getCity());


			$reviews = $this->getReviews();
			$items = $reviews['items'];
			$has_next = $reviews['has_next'];

			if (!$items) {
				throw new Exception();
			}
		} else {
			$page = (int)app()->request()->processPostParams(['page' => 'int'])['page'];
			$reviews = $this->getReviews(null, $page);
			$items = $reviews['items'];
			$has_next = $reviews['has_next'];

			$this->view()
					->set('items', $items)
					->setTemplate('bottom_block_reviews_ajax');

			die(json_encode(['html' => $this->view()->render(), 'has_next' => $has_next, 'page' => ($page + 1)]));
		}

		return $this->view()
						->set('items', $items)
						->set('has_next', $has_next)
						->set('tabs', app()->tabs()->render())
						->set('firm', $this->model())
						->setTemplate('bottom_block_reviews')
						->render();
	}

	protected function renderPromoBlock() {
		app()->tabs()->setActiveTab(4)->setSortOptions([]);

		app()->breadCrumbs()->setElem('Акции', app()->linkFilter($this->model()->linkItem(), ['mode' => 'promo']));
		if ($this->params['id_promo'] === null) {
			app()->stat()->addObject(StatObject::PROMO_LIST, $this->model());
			app()->metadata()
					->setMetatag('keywords', 'Текущие акции, скидки, распродажи фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity())
					->setMetatag('description', 'Узнайте подробности об акциях, скидках на товары и услуги фирмы '.app()->metadata()->getTitle().' ('.$this->model()->getCity().')')
					->setHeader('Текущие акции, скидки, распродажи фирмы '.app()->metadata()->getTitle())
					->setTitle('Акции, скидки, распродажи фирмы '.app()->metadata()->getTitle().', '.$this->model()->getCity());

			$items = $this->getPromo();
			if (!$items) {
				throw new Exception();
			}
			$this->view()
					->set('items', $this->getPromo())
					->set('tabs', app()->tabs()->render())
					->set('firm', $this->model())
					->setTemplate('bottom_block_promo');
		} else {
			$fpc = new FirmPromo();
			$this->view()
					->set('content', $fpc->renderPromoItem($this->params['id_promo'], app()->linkFilter($this->model()->link(), ['mode' => 'promo', 'id_promo' => $this->params['id_promo']])))
					->set('tabs', app()->tabs()->render())
					->set('firm', $this->model())
					->set('inside_bread_crumbs', app()->breadCrumbs()->renderBottom())
					->setTemplate('bottom_block_promo_show');
		}



		return $this->view()->render();
	}
    
    protected function renderFirmBranchBlock() {
		app()->tabs()->setActiveTab(5)->setSortOptions([]);

		app()->breadCrumbs()->setElem('Другие адреса фирмы', app()->linkFilter($this->model()->linkItem(), ['mode' => 'firm-branch']));
        app()->metadata()
                ->setMetatag('keywords', 'Другие адреса и телефоны фирмы '.app()->metadata()->getTitle())
                ->setMetatag('description', 'Посмотрите другие адреса фирмы '.app()->metadata()->getTitle())
                ->setHeader('Другие адреса  фирмы '.app()->metadata()->getTitle())
                ->setTitle('Другие адреса фирмы '.app()->metadata()->getTitle())
                ->setCanonicalUrl($this->model()->link());
        $items = $this->model()->getFirmBranches();
        if (!$items) {
            throw new Exception();
        }
        
        app()->setUseMap(true);

        //Филиалы фирмы
        $presenter = new FirmItems();
        $presenter
                ->setLimit(1000)
                ->setPage($this->getPage());

        $firm_branches_on_map = [$this->model()->id() => $this->model()];
        if ($items) {
            foreach ($items as $id_city => $_items) {
                foreach ($_items as $firm_branch) {
                    $firm_branches_on_map[$firm_branch->id()] = $firm_branch;
                }
            }
        }

        $presenter
                ->setModel($this->model())
                ->findByFirmBranchesIds($firm_branches_on_map, ['mode' => 'map'], false);

        $firm_branches_on_map = $presenter->setItemsTemplate('bottom_block_common_info_map')
                ->setParams('show_company_activity', 0)
                ->renderItems();
        
        $this->view()
                ->set('tabs', app()->tabs()->render())
                ->set('item', $this->model())
                ->set('items', $items)
                ->set('firm_branches_on_map', $firm_branches_on_map)
                ->set('firm', $this->model())
                ->setTemplate('bottom_block_firm_branch');

		return $this->view()->render();
	}

	protected function getQuestions($limit = 3) {
		$results = [];

		$db = new \Sky4\Db\Connection('727373_dev');
		$rows = $db->query()
				->setText("SELECT `question` FROM `question_thread` WHERE "
                        . "`organizations_ids` REGEXP '^".$this->model()->id().",' "
                        . "OR `organizations_ids` REGEXP ',".$this->model()->id()."$' "
                        . "OR `organizations_ids` REGEXP ',".$this->model()->id().",' "
                        . "OR `organizations_ids` = '".$this->model()->id()."' "
                        . "ORDER BY `time_inserting` DESC LIMIT 0,10")
                        //. "`organizations_ids` LIKE '%,".$this->model()->id_firm()."-".$this->model()->id_service().",%' OR `organizations_ids` LIKE '".$this->model()->id_firm()."-".$this->model()->id_service().",%' OR `organizations_ids` LIKE '%,".$this->model()->id_firm()."-".$this->model()->id_service()."' OR `organizations_ids` = '".$this->model()->id_firm()."-".$this->model()->id_service()."' ORDER BY `time_inserting` DESC LIMIT 0,10")
				->fetch();

		if ($rows) {
			$id_questions = [];
			foreach ($rows as $row) {
				$id_questions[] = $row['question'];
			}

			if ($id_questions) {
				$questions = $db->query()
						->setText("SELECT `id`, `text`, `mnemonick` FROM `question` WHERE `flag_on_site` = 1 AND `flag_is_closed` = 1 AND `id` IN (".implode(',', $id_questions).") ORDER BY time_inserting DESC LIMIT 0,".(int)$limit)
						->fetch();

				foreach ($questions as $q) {
					$results[$q['id']] = ['text' => str()->firstCharToUpper($q['text']), 'mnemonick' => $q['mnemonick']];
				}
			}
		}

		return $results;
	}

	protected function getVideos($count = null, $exception_id = null) {
		$fv = new FirmVideo();
		$where = ['AND', '`id_firm` = :id_firm'];
		$params = [':id_firm' => $this->model()->id()];
		if ($exception_id !== null) {
			$where[] = 'id != :exception_id';
			$params[':exception_id'] = $exception_id;
		}

		return $fv->reader()
						->setWhere($where, $params)
						->setOrderBy('`timestamp_inserting` DESC')
						->setLimit($count)
						->objects();
	}

	/**
	 * @deprecated 
	 * @param type $count
	 * @return type
	 */
	protected function getVideosByCity($count = null) {
		$fv = new FirmVideo();
		$conds_city_id = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$fv->reader()
				->setWhere($conds_city_id['where'], $conds_city_id['params'])
				->setOrderBy('`timestamp_inserting` DESC');

		if ($count !== null) {
			$fv->reader()->setLimit((int)$count);
		}

		return $fv->reader()->objects();
	}
    
    protected function getReviewsCount() {
        $fr = new FirmReview();

        $where = [
			'AND',
			'flag_is_active = :flag_is_active',
			'id_firm = :id_firm'
		];

		$params = [
			':flag_is_active' => 1,
			':id_firm' => $this->model()->id()
		];

		$_items = $fr->reader()
				->setWhere($where, $params)
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$_count = $fr->reader()
				->setWhere($where, $params)
				->count();
        return $_count;
    }


	protected function getReviews($limit = null, $page = 0) {
		$fr = new FirmReview();
		if ($limit === null) {
			$limit = (int)app()->config()->get('app.firm.reviews.count', 3);
		}

		$where = [
			'AND',
			'flag_is_active = :flag_is_active',
			'id_firm = :id_firm'
		];

		$params = [
			':flag_is_active' => 1,
			':id_firm' => $this->model()->id()
		];

		$_items = $fr->reader()
				->setWhere($where, $params)
				->setLimit($limit, (int)$page * $limit)
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$_count = $fr->reader()
				->setWhere($where, $params)
				->count();

		$items = [];
		foreach ($_items as $item) {
			$date = $item->val('timestamp_inserting');
			$items[] = [
				'user' => $item->val('user_name'),
				'date' => DeprecatedDateTime::day($date).' '.DeprecatedDateTime::monthName($date, 1).' '.DeprecatedDateTime::year($date),
				'score' => $item->val('score'),
				'text' => $item->val('text'),
				'reply_date' => DeprecatedDateTime::day($item->val('reply_timestamp')).' '.DeprecatedDateTime::monthName($item->val('reply_timestamp'), 1).' '.DeprecatedDateTime::year($item->val('reply_timestamp')),
				'reply_user_name' => $item->val('reply_user_name'),
				'reply_text' => $item->val('reply_text')
			];
		}

		return ['items' => $items, 'has_next' => $_count > ($page * $limit) + $limit];
	}

	protected function getPromo() {
		$firm_promo = new \App\Model\FirmPromo();

		$where = [
			'AND',
			'flag_is_active = :flag_is_active',
			'id_firm = :id_firm',
			'timestamp_ending >= :timestamp',
		];

		$params = [
			':flag_is_active' => 1,
			':id_firm' => $this->model()->id(),
			':timestamp' => DeprecatedDateTime::now()
		];

		$_items = $firm_promo->reader()
				->setWhere($where, $params)
				->objects();

		$files = [];
		foreach ($_items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
		}

		$files = Utils::getObjectsByIds($files);


		$items = [];
		foreach ($_items as $item) {
			$image_key = Utils::getFirstCompositeId($item->val('image'));
			$items[] = \App\Model\FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
		}

		return $items;
	}

	public static function getSortingOptions() {
		return FirmController::getSortingOptions();
	}

}
