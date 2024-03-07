<?php

class Items extends CController {

	protected $filter_vals = [];
	protected $items = [];
	protected $items_template = 'items_presenter_items';
	protected $items_template_subdir_name = 'common';
	protected $limit = 0;
	protected $modes = [];
	protected $order = [];
	protected $page = 1;
	protected $template = 'items_presenter';
	protected $template_subdir_name = 'common';
	// ----
	protected $filter = null;
	protected $filter_available = true;
	protected $pagination = null;
	protected $pagination_available = true;
	protected $sorting_available = true;
	protected $view_modes_available = true;

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

	public function getTemplate() {
		return $this->template;
	}

	public function getTemplateSubdirName() {
		return $this->template_subdir_name;
	}

	public function orderableFields() {
		return [];
	}

	public function setFilterVals($vals) {
		$this->filter_vals = [];
		if (is_array($vals)) {
			foreach ($vals as $field_name => $val) {
				$this->filter_vals[(string) $field_name] = $val;
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

	public function setLimit($limit) {
		$this->limit = (int) $limit;
		return $this;
	}

	public function setMode($mode) {
		$this->modes[] = (string) $mode;
		return $this;
	}

	public function setModes($modes) {
		$this->modes = [];
		if (is_array($modes)) {
			foreach ($modes as $mode) {
				$this->setMode($mode);
			}
		}
		return $this;
	}

	public function setOrder($fields) {
		$this->order = [];
		if (is_array($fields)) {
			foreach ($fields as $field_name => $order_direction) {
				$order_direction = str()->toUpper($order_direction);
				$this->order[(string) $field_name] = ($order_direction === 'DESC') ? 'DESC' : 'ASC';
			}
		} elseif (is_string($fields)) {
			$fields = explode(',', $fields);
			foreach ($fields as $field) {
				if (preg_match('/^(.+)\-(asc|desc)$/iu', $field, $matches)) {
					$order_direction = str()->toUpper($matches[2]);
					$this->order[(string) $matches[1]] = ($order_direction === 'DESC') ? 'DESC' : 'ASC';
				}
			}
		}
		return $this;
	}

	public function setPage($page) {
		$this->page = (int) $page;
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

	// -------------------------------------------------------------------------

	/**
	 * @return \Sky4\Model\Filter
	 */
	public function filter() {
		if ($this->filter === null) {
			$this->filter = $this->model()->filter();
		}
		return $this->filter;
	}

	/**
	 * @return \ItemsPresenter|bool
	 */
	public function filterAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->filter_available = $flag;
			return $this;
		}
		return $this->filter_available;
	}

	/**
	 * @return \App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new \App\Classes\Pagination();
			$this->pagination->setLimit(10)
					->setNearbyPages(3);
		}
		return $this->pagination;
	}

	/**
	 * @return \ItemsPresenter|bool
	 */
	public function paginationAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->pagination_available = $flag;
			return $this;
		}
		return $this->pagination_available;
	}

	public function setFilter(\Sky4\Model\Filter $filter) {
		$this->filter = $filter;
		return $this;
	}

	public function setPagination(\App\Classes\Pagination $pagination) {
		$this->pagination = $pagination;
		return $this;
	}

	/**
	 * @return \ItemsPresenter|bool
	 */
	public function sortingAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->sorting_available = $flag;
			return $this;
		}
		return $this->sorting_available;
	}

	/**
	 * @return \ItemsPresenter|bool
	 */
	public function viewModesAvailable($flag = null) {
		if (is_bool($flag)) {
			$this->view_modes_available = $flag;
			return $this;
		}
		return $this->view_modes_available;
	}

	// -------------------------------------------------------------------------

	/**
	 * @return \Sky4\Model
	 */
	public function relObject() {
		if ($this->rel_object === null) {
			throw new CException('Необходимо указать связанный объект');
		}
		return $this->rel_object;
	}

	public function setRelObject(\Sky4\Model $rel_object) {
		$this->rel_object = $rel_object;
		return $this;
	}

	// -------------------------------------------------------------------------

	public function find() {
		$_where = array('AND');
		$_order_by = $this->assembleOrderBy();
		$_params = [];

		if ($this->filterAvailable()) {
			$this->filter()->setVals($this->getFilterVals());
			$conds = $this->filter()->assembleConds();
			$_where = $conds['where'];
			$_params = $conds['params'];
		}
		if (empty($_where)) {
			$_where[] = 'AND';
		}
		if (($this->rel_object !== null) && $this->relObject()->exists()) {
			$_where[] = '`model_alias` = :model_alias';
			$_where[] = '`object_id` = :object_id';
			$_params[':model_alias'] = $this->relObject()->alias();
			$_params[':object_id'] = $this->relObject()->id();
		}

		if ($this->paginationAvailable()) {
			$this->pagination()
					->setLimit($this->getLimit())
					->setPage($this->getPage())
					->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
					->calculateParams()
					->renderElems();
			$this->items = $this->model()->reader()
					->setWhere($_where, $_params)
					->setOrderBy($_order_by)
					->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
					->objects();
		} else {
			$this->items = $this->model()->reader()
					->setWhere($_where, $_params)
					->setOrderBy($_order_by)
					->objects();
		}

		return $this;
	}

	public function render($return_array = false) {
		if ($return_array) {
			return array(
				'filter' => $this->filterAvailable() ? $this->filter()->render() : '',
				'items' => $this->renderItems(),
				'pagination' => $this->paginationAvailable() ? $this->pagination()->render() : '',
				'sorting' => $this->sortingAvailable() ? $this->renderSorting() : ''
			);
		}
		return $this->view()
						->set('filter', $this->filterAvailable() ? $this->filter()->render() : '')
						->set('item', $this->model())
						->set('items', $this->renderItems())
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('pagination', $this->paginationAvailable() ? $this->pagination()->render() : '')
						->set('sorting', $this->sortingAvailable() ? $this->renderSorting() : '')
						->setTemplate($this->getTemplate(), $this->getTemplateSubdirName())
						->render();
	}

	public function renderItems() {
		return $this->view()
						->set('items', $this->getItems())
						->set('pagination', $this->pagination()->render())
						->setTemplate($this->getItemsTemplate(), $this->getItemsTemplateSubdirName())
						->render();
	}

	public function renderSorting() {
		$fields = $this->model()->getFields();
		$orderable_fields = [];
		$orderable_fields_names = $this->model()->getOrderableFieldsNames();
		foreach ($fields as $field_name => $field_props) {
			$field_name = (string) $field_name;
			if (in_array($field_name, $orderable_fields_names) && isset($field_props['label'])) {
				$orderable_fields[$field_name] = array('label' => $field_props['label']);
			}
		}
		return $this->view()
						->set('order', $this->getOrder())
						->set('orderable_fields', $orderable_fields)
						->setTemplate('items_presenter_sorting', 'common')
						->render();
	}

	public function renderTableHead() {
		$order = $this->getOrder();
		$orderable_fields = $this->orderableFields();
		foreach ($orderable_fields as $orderable_field_name => $orderable_field_props) {
			$order_direction = '';
			if (isset($order[$orderable_field_name])) {
				$order_direction = (str()->toLower($order[$orderable_field_name]) === 'asc') ? 'asc' : 'desc';
			}
			$orderable_fields[$orderable_field_name]['order_direction'] = $order_direction;
		}
		return $this->view()
						->set('orderable_fields', $orderable_fields)
						->setTemplate('items_presenter_table_head', 'common')
						->render();
	}

	// -------------------------------------------------------------------------

	protected function assembleOrderBy() {
		$order = [];
		foreach ($this->getOrder() as $field_name => $order_direction) {
			$order[] = '`' . (string) $field_name . '` ' . (string) $order_direction;
		}
		return !empty($order) ? implode(', ', $order) : null;
	}

}
