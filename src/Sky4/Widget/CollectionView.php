<?php

namespace Sky4\Widget;

use Sky4\Controller,
	Sky4\Exception,
	Sky4\Helper\StringHelper,
	Sky4\Model,
	Sky4\Model\Filter,
	Sky4\View,
	Sky4\Widget\Pagination;

class CollectionView extends Controller {

	protected $filter = null;
	protected $filter_available = true;
	protected $filter_enabled = false;
	protected $filter_vals = [];
	protected $items = [];
	protected $items_template = 'presenter_items';
	protected $items_template_subdir_name = 'presenter';
	protected $items_total_count = 0;
	protected $limit = 10;
	protected $local_menu_available = true;
	protected $modes = [];
	protected $order = [];
	protected $page = 1;
	protected $pagination = null;
	protected $pagination_available = true;
	protected $rel_model = null;
	protected $rels_available = true;
	protected $scopes = [];
	protected $sorting_available = true;
	protected $structure_view_mode = '';
	protected $structure_view_modes_available = true;
	protected $template = 'presenter';
	protected $template_subdir_name = 'presenter';
	protected $title = '';
	protected $view_mode = '';
	protected $view_modes_available = true;
	// ----
	protected $_order_by = null;
	protected $_params = null;
	protected $_where = null;

	public function assembleConds() {
		$this->_params = null;
		$this->_where = null;

		$scopes = $this->model()->getScopes();
		if ($scopes) {
			foreach ($this->scopes as $scope) {
				if (isset($scopes[$scope]) && isset($scopes[$scope]['params']) && $scopes[$scope]['params'] && isset($scopes[$scope]['where']) && $scopes[$scope]['where']) {
					if (!$this->_where) {
						$this->_params = [];
						$this->_where = ['AND'];
					}
					$this->_params = array_merge($this->_params, $scopes[$scope]['params']);
					$this->_where[] = $scopes[$scope]['where'];
				}
			}
		}

		if ($this->filterAvailable()) {
			$this->filter()->setVals($this->getFilterVals());
			$conds = $this->filter()->assembleConds();
			if ($conds['params'] && $conds['where']) {
				if (!$this->_where) {
					$this->_params = [];
					$this->_where = ['AND'];
				}
				$this->_params = array_merge($this->_params, $conds['params']);
				$this->_where[] = $conds['where'];
			}
		}

		if ($this->hasRelModel() && $this->relModel()->exists()) {
			if (!$this->_where) {
				$this->_params = [];
				$this->_where = ['AND'];
			}
			$this->_where[] = '`model_alias` = :model_alias';
			$this->_where[] = '`object_id` = :object_id';
			$this->_params[':model_alias'] = $this->relModel()->alias();
			$this->_params[':object_id'] = $this->relModel()->id();
		}

		return $this;
	}

	public function assembleOrderBy() {
		$_order_by = [];
		foreach ($this->getOrder() as $field_name => $order_direction) {
			$_order_by[] = '`' . (string) $field_name . '` ' . (string) $order_direction;
		}
		$this->_order_by = $_order_by ? implode(', ', $_order_by) : null;
		return $this;
	}

	public function find() {
		$this->assembleConds()
				->assembleOrderBy();

		if ($this->paginationAvailable()) {
			$items_total_count = 0;
			if ($this->model()->hasStructure()) {
				if ($this->getStructureViewMode() === 'list') {
					if (!$this->model()->exists()) {
						$items_total_count = $this->model()
								->reader()
								->setWhere($this->_where, $this->_params)
								->count();
					}
				} elseif ($this->model()->exists()) {
					$items_total_count = $this->model()
							->structure()
							->reader()
							->setWhere($this->_where, $this->_params)
							->childrenCount($this->model()->val('node_level') + 1);
				} else {
					$items_total_count = $this->model()
							->structure()
							->reader()
							->setWhere($this->_where, $this->_params)
							->parentsCount($this->_where, $this->_params);
				}
			} elseif (!$this->model()->exists()) {
				$items_total_count = $this->model()
						->reader()
						->setWhere($this->_where, $this->_params)
						->count();
			}
			$this->setItemsTotalCount($items_total_count);
			$this->pagination()
					->setLimit($this->getLimit())
					->setPage($this->getPage())
					->setTotalRecords($this->getItemsTotalCount())
					->calculateParams()
					->renderElems();
		}

		$_limit = $this->paginationAvailable() ? $this->pagination()->getLimit() : null;
		$_offset = $this->paginationAvailable() ? $this->pagination()->getOffset() : null;

		$items = [];
		if ($this->model()->hasStructure()) {
			if ($this->getStructureViewMode() === 'list') {
				if (!$this->model()->exists()) {
					$items = $this->model()
							->reader()
							->setWhere($this->_where, $this->_params)
							->setOrderBy($this->_order_by)
							->setLimit($_limit, $_offset)
							->objects();
				}
			} elseif ($this->model()->exists()) {
				$items = $this->model()
						->structure()
						->reader()
						->setWhere($this->_where, $this->_params)
						->setOrderBy($this->_order_by)
						->setLimit($_limit, $_offset)
						->childrenObjects($this->model()->val('node_level') + 1);
			} else {
				$items = $this->model()
						->structure()
						->reader()
						->setWhere($this->_where, $this->_params)
						->setOrderBy($this->_order_by)
						->setLimit($_limit, $_offset)
						->parentsObjects();
			}
		} elseif (!$this->model()->exists()) {
			$items = $this->model()
					->reader()
					->setWhere($this->_where, $this->_params)
					->setOrderBy($this->_order_by)
					->setLimit($_limit, $_offset)
					->objects();
		}
		$this->processItems($items);

		if (!$this->paginationAvailable()) {
			$this->setItemsTotalCount(count($this->items));
		}

		return $this;
	}

	public function processItems($items) {
		return $this->setItems($items);
	}

	public function render() {
		$vars = $this->renderInArray();
		foreach ($vars as $var_name => $content) {
			$this->view()->set($var_name, $content);
		}
		return $this->view()
						->set('attrs', $this->attrs())
						->set('model', $this->model())
						->set('presenter', $this)
						->set('title', $this->getTitle())
						->setTemplate($this->getTemplate(), $this->getTemplateSubdirName())
						->render();
	}

	public function renderInArray() {
		return [
			'filter' => ($this->filterAvailable() && $this->filterEnabled()) ? $this->filter()->render() : '',
			'items' => $this->renderItems(),
			'local_menu' => $this->localMenuAvailable() ? $this->renderLocalMenu() : '',
			'pagination' => $this->paginationAvailable() ? $this->pagination()->render() : '',
			'rels' => ($this->model()->exists() && $this->relsAvailable()) ? $this->renderRels() : '',
			'sorting' => $this->sortingAvailable() ? $this->renderSorting() : '',
			'structure_view_modes' => $this->structureViewModesAvailable() ? $this->renderStructureViewModes() : '',
			'view_modes' => $this->viewModesAvailable() ? $this->renderViewModes() : '',
			// ----
			'items_total_count' => $this->getItemsTotalCount()
		];
	}

	public function renderItems() {
		$template = $this->getItemsTemplate();
		switch ($this->getViewMode()) {
			case 'icons':
				$template .= '_' . $this->getViewMode();
				break;
		}
		return $this->view()
						->set('cols', $this->cols())
						->set('fields', $this->model()->getFields())
						->set('items', $this->getItems())
						->set('model', $this->model())
						->set('order', $this->getOrder())
						->set('orderable_fields_names', $this->model()->getOrderableFieldsNames())
						->set('page', $this->pagination()->getPage())
						->set('presenter', $this)
						->set('total_pages', $this->pagination()->getTotalPages())
						->setTemplate($template, $this->getItemsTemplateSubdirName())
						->render();
	}

	public function renderLocalMenu() {
		return $this->view()
						->set('actions', $this->actions())
						->set('model', $this->model())
						->set('presenter', $this)
						->set('structure_view_mode', $this->getStructureViewMode())
						->set('structure_view_modes', $this->structureViewModes())
						->set('view_mode', $this->getViewMode())
						->set('view_modes', $this->viewModes())
						->setTemplate('presenter_local_menu', 'presenter')
						->render();
	}

	public function renderRels() {
		return $this->view()
						->set('default_rel_name', $this->defaultRelName())
						->set('model', $this->model())
						->set('presenter', $this)
						->set('rels', $this->rels())
						->setTemplate('presenter_rels', 'presenter')
						->render();
	}

	public function renderSorting() {
		return $this->view()
						->set('fields', $this->model()->getFields())
						->set('model', $this->model())
						->set('order', $this->getOrder())
						->set('orderable_fields_names', $this->model()->getOrderableFieldsNames())
						->set('presenter', $this)
						->setTemplate('presenter_sorting', 'presenter')
						->render();
	}

	public function renderStructureViewModes() {
		return $this->view()
						->set('structure_view_mode', $this->getStructureViewMode())
						->set('structure_view_modes', $this->structureViewModes())
						->setTemplate('presenter_structure_view_modes', 'presenter')
						->render();
	}

	public function renderViewModes() {
		return $this->view()
						->set('view_mode', $this->getViewMode())
						->set('view_modes', $this->viewModes())
						->setTemplate('presenter_view_modes', 'presenter')
						->render();
	}

	// -------------------------------------------------------------------------

	public function actions() {
		return [];
	}

	public function attrs() {
		$attrs = [
			'class' => 'b-presenter js-presenter',
			'data-model-alias' => $this->model()->alias()
		];
		if ($this->model()->exists()) {
			$attrs['data-object-id'] = $this->model()->id();
		}
		return $attrs;
	}

	public function cols() {
		$result = [];
		$cols = $this->model()->getCols();
		$fields = $this->model()->getFields();
		foreach ($cols as $field_name => $col_props) {
			$_field_name = '';
			$_col_props = [];
			if (is_array($col_props)) {
				$_field_name = $field_name;
				$_col_props = $col_props;
			} elseif (is_string($col_props)) {
				$_field_name = $col_props;
			}
			if ($_field_name) {
				if (!isset($_col_props['label'])) {
					if (isset($fields[$_field_name]) && isset($fields[$_field_name]['label'])) {
						$_col_props['label'] = $fields[$_field_name]['label'];
					} else {
						$_col_props['label'] = '';
					}
				}
				$result[$_field_name] = $_col_props;
			}
		}
		return $result;
	}

	public function defaultRelName() {
		return $this->model()->defaultRelName();
	}

	public function rels() {
		return $this->model()->rels();
	}

	public function structureViewModes() {
		return [
			'structure' => 'Отображение деревом',
			'list' => 'Отображение списком'
		];
	}

	public function viewModes() {
		return [
			'table' => 'Табличное отображение',
			'icons' => 'Отображение иконками'
		];
	}

	// -------------------------------------------------------------------------

	public function addMode($mode) {
		$this->modes[] = (string) $mode;
		return $this;
	}

	public function addScope($scope) {
		$this->scopes[] = (string) $scope;
		return $this;
	}

	/**
	 * @return Filter
	 */
	public function filter() {
		if ($this->filter === null) {
			$this->filter = $this->model()->filter();
			$this->prepareFilter();
		}
		return $this->filter;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function filterAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->filter_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->filter_available = ($flag === 1);
			return $this;
		}
		return $this->filter_available;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function filterEnabled($flag = null) {
		if (is_bool($flag)) {
			$this->filter_enabled = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->filter_enabled = ($flag === 1);
			return $this;
		}
		return $this->filter_enabled;
	}

	public function getFilterVal($field_name, $default_val = null) {
		return isset($this->filter_vals[(string) $field_name]) ? $this->filter_vals[(string) $field_name] : $default_val;
	}

	public function getFilterVals() {
		return $this->filter_vals;
	}

	public function getItems() {
		return $this->items;
	}

	public function getItemsTemplate() {
		return $this->items_template;
	}

	public function getItemsTemplateSubdirName() {
		return $this->items_template_subdir_name;
	}

	public function getItemsTotalCount() {
		return $this->items_total_count;
	}

	public function getLimit() {
		return $this->limit;
	}

	public function getModes() {
		return $this->modes;
	}

	public function getOrder() {
		return $this->order;
	}

	public function getPage() {
		return $this->page;
	}

	public function getScopes() {
		return $this->scopes;
	}

	public function getStructureViewMode() {
		return $this->structure_view_mode;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function getTemplateSubdirName() {
		return $this->template_subdir_name;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getViewMode() {
		return $this->view_mode;
	}

	public function hasMode($mode) {
		return in_array((string) $mode, $this->modes);
	}

	public function hasRelModel() {
		return ($this->rel_model !== null);
	}

	public function hasScope($scope) {
		return in_array((string) $scope, $this->scopes);
	}

	/**
	 * @return CollectionView|bool
	 */
	public function localMenuAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->local_menu_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->local_menu_available = ($flag === 1);
			return $this;
		}
		return $this->local_menu_available;
	}

	/**
	 * @return Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function paginationAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->pagination_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->pagination_available = ($flag === 1);
			return $this;
		}
		return $this->pagination_available;
	}

	public function prepareFilter() {
		if ($this->filter === null) {
			throw new Exception('Фильтр не установлен');
		}
		$this->filter->setControls([
			'filtrate' => [
				'attrs' => [
					'class' => 'js-action',
					'data-actions' => 'presenter.find'
				],
				'elem' => 'button',
				'label' => 'Найти'
			],
			'reload' => [
				'attrs' => [
					'class' => 'js-action js-filter-reloader',
					'data-actions' => 'presenter.reload-filter'
				],
				'elem' => 'button',
				'label' => 'Сброс'
			]
		]);
		return $this;
	}

	/**
	 * @return Model
	 */
	public function relModel() {
		if ($this->rel_model === null) {
			throw new Exception('Необходимо указать связанную модель');
		}
		return $this->rel_model;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function relsAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->rels_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->rels_available = ($flag === 1);
			return $this;
		}
		return $this->rels_available;
	}

	public function setFilter(Filter $filter) {
		$this->filter = $filter;
		return $this->prepareFilter();
	}

	public function setFilterVal($field_name, $val) {
		$this->filter_vals[(string) $field_name] = $val;
		return $this;
	}

	public function setFilterVals($vals) {
		$this->filter_vals = [];
		if (is_array($vals)) {
			foreach ($vals as $field_name => $val) {
				$this->setFilterVal($field_name, $val);
			}
		}
		return $this;
	}

	public function setItems($items) {
		$this->items = (array) $items;
		return $this;
	}

	public function setItemsTemplate($template) {
		$this->items_template = (string) $template;
		return $this;
	}

	public function setItemsTemplateSubdirName($template_subdir_name) {
		$this->items_template_subdir_name = (string) $template_subdir_name;
		return $this;
	}

	public function setItemsTotalCount($items_total_count) {
		$this->items_total_count = (int) $items_total_count;
		return $this;
	}

	public function setLimit($limit) {
		$this->limit = (int) $limit;
		return $this;
	}

	public function setModes($modes) {
		$this->modes = [];
		if (is_array($modes)) {
			foreach ($modes as $mode) {
				$this->addMode($mode);
			}
		}
		return $this;
	}

	public function setOrder($fields) {
		$this->order = [];
		if (is_array($fields)) {
			foreach ($fields as $field_name => $order_direction) {
				$this->order[(string) $field_name] = (StringHelper::toUpper($order_direction) === 'DESC') ? 'DESC' : 'ASC';
			}
		} elseif (is_string($fields)) {
			$fields = explode(',', $fields);
			foreach ($fields as $field) {
				if (preg_match('/^(.+)\-(asc|desc)$/iu', $field, $matches)) {
					$this->order[(string) $matches[1]] = (StringHelper::toUpper($matches[2]) === 'DESC') ? 'DESC' : 'ASC';
				}
			}
		}
		return $this;
	}

	public function setPage($page) {
		$this->page = (int) $page;
		return $this;
	}

	public function setPagination(Pagination $pagination) {
		$this->pagination = $pagination;
		return $this;
	}

	public function setRelModel(Model $rel_model) {
		$this->rel_model = $rel_model;
		return $this;
	}

	public function setScopes($scopes) {
		$this->scopes = [];
		if (is_array($scopes)) {
			foreach ($scopes as $scope) {
				$this->addScope($scope);
			}
		}
		return $this;
	}

	public function setStructureViewMode($structure_view_mode) {
		$this->structure_view_mode = (string) $structure_view_mode;
		return $this;
	}

	public function setTemplate($template) {
		$this->template = (string) $template;
		return $this;
	}

	public function setTemplateSubdirName($template_subdir_name) {
		$this->template_subdir_name = (string) $template_subdir_name;
		return $this;
	}

	public function setTitle($title) {
		$this->title = (string) $title;
		return $this;
	}

	public function setViewMode($view_mode) {
		$this->view_mode = (string) $view_mode;
		return $this;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function sortingAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->sorting_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->sorting_available = ($flag === 1);
			return $this;
		}
		return $this->sorting_available;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function structureViewModesAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->structure_view_modes_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->structure_view_modes_available = ($flag === 1);
			return $this;
		}
		return $this->structure_view_modes_available;
	}

	/**
	 * @return View
	 */
	public function view() {
		if ($this->view === null) {
			$this->view = new $this->view_name();
			$this->view->setBasicSubdirName($this->getTemplateSubdirName());
		}
		return $this->view;
	}

	/**
	 * @return CollectionView|bool
	 */
	public function viewModesAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->view_modes_available = $flag;
			return $this;
		} elseif (is_int($flag)) {
			$this->view_modes_available = ($flag === 1);
			return $this;
		}
		return $this->view_modes_available;
	}

}
