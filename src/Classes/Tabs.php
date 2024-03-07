<?php

namespace App\Classes;

class Tabs {

	protected $additional_wrapper = null;
	protected $active_sort_option = null;
	protected $active_group_option = null;
	protected $active_tab_index = null;
	protected $display_modes = false;
	protected $filters = [];
	protected $link = '';
	protected $sort_options = [];
	protected $group_options = [];
	protected $tabs = [];
	protected $tabs_numeric_values = null;
	protected $template = 'tabs';
	protected $template_sorting = 'tabs_sorting';
	protected $template_grouping = 'tabs_grouping';
	protected $template_dir = 'common';
	//
	protected $view = null;

	public function render($hide_sorting = null, $lk = false) {
		$filters = $this->filters ? $this->filters : $_GET;
		if (isset($filters['url'])) unset($filters['url']);
		if (isset($filters['sorting'])) unset($filters['sorting']);

		$view = $this->view()
						->set('additional_wrapper', $this->getAdditionalWrapper())
						->set('active_sort_option', $this->getActiveSortOption())
						->set('active_group_option', $this->getActiveGroupOption())
						->set('active_tab_index', $this->active_tab_index)
						->set('filters', $filters)
						->set('link', $this->getLink())
						->set('tabs', $this->getTabs())
						->set('counters', $this->getTabsNumericValues())
						->set('sorters', $this->getSortOptions())
						->set('groupers', $this->getGroupOptions())
						->set('show_display_modes', $this->display_modes)
						->set('hide_sorting', $hide_sorting === null ? false : true);
        
        if ($lk) {
            $view->setDirPath(APP_DIR_PATH.'/src/views2');
        }
        
        return $view->setTemplate($this->getTemplate())
						->render();
	}

	public function renderSorting($lk = false) {
		$filters = $this->filters ? $this->filters : $_GET;
		if (isset($filters['url'])) unset($filters['url']);
		if (isset($filters['sorting'])) unset($filters['sorting']);

		$view = $this->view()
						->set('active_sort_option', $this->getActiveSortOption())
						->set('filters', $filters)
						->set('link', $this->getLink())
						->set('sorters', $this->getSortOptions());
        if ($lk) {
            $view->setDirPath(APP_DIR_PATH.'/src/views2');
        }
        return $view->setTemplate($this->getTemplateSorting())
						->render();
	}

	public function renderGrouping() {
		$filters = $this->filters ? $this->filters : $_GET;
		if (isset($filters['url'])) unset($filters['url']);
		if (isset($filters['group'])) unset($filters['group']);

		return $this->view()
						->set('active_group_option', $this->getActiveGroupOption())
						->set('filters', $filters)
						->set('link', $this->getLink())
						->set('groupers', $this->getGroupOptions())
						->setTemplate($this->getTemplateGrouping())
						->render();
	}

	public function getLink() {
		return $this->link;
	}

	public function getActiveSortOption() {
		return $this->active_sort_option;
	}

	public function getAdditionalWrapper() {
		return $this->additional_wrapper;
	}

	public function getActiveGroupOption() {
		return $this->active_group_option;
	}

	public function getActiveTab() {
		return $this->active_tab;
	}

	public function getSortOptions() {
		return $this->sort_options;
	}

	public function getGroupOptions() {
		return $this->group_options;
	}

	public function getTabsNumericValues() {
		return $this->tabs_numeric_values;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function getTemplateSorting() {
		return $this->template_sorting;
	}

	public function getTemplateGrouping() {
		return $this->template_grouping;
	}

	public function getTabs() {
		return $this->tabs;
	}

	public function setActiveTab($index) {
		$this->active_tab_index = (int)$index;
		return $this;
	}

	public function setActiveTabByMode($mode) {
		$result = 0;
		if ($mode === null) {
			$result = 0;
		} else {
			foreach ($this->tabs as $k => $tab) {
				if (isset($tab['mode']) && $tab['mode'] === $mode) {
					$result = $k;
				}
			}
		}
		$this->active_tab_index = (int)$result;
		return $this;
	}

	public function setActiveSortOption($key) {
		$this->active_sort_option = $key;
		return $this;
	}

	public function setActiveGroupOption($key) {
		$this->active_group_option = $key;
		return $this;
	}

	public function setDisplayMode($param) {
		$this->display_modes = (bool)$param;
		return $this;
	}

	public function setFilters($filters) {
		$this->filters = $filters;
		return $this;
	}

	public function setLink($url) {
		$this->link = $url;
		return $this;
	}

	public function setSortOptions($sort_options) {
		$this->sort_options = $sort_options;
		return $this;
	}

	public function setGroupOptions($group_options) {
		$this->group_options = $group_options;
		return $this;
	}

	public function setTabs($tabs) {
		$this->tabs = $tabs;
		return $this;
	}

	public function setAdditionalWrapper($class_name) {
		$this->additional_wrapper = (string)$class_name;
		return $this;
	}

	public function setTabsNumericValues($tabs_numeric_values) {
		$this->tabs_numeric_values = $tabs_numeric_values;
		return $this;
	}

	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}

	public function setTemplateDir($template_dir) {
		$this->template_dir = $template_dir;
		return $this;
	}

	public function view() {
		if ($this->view === null) {
			$this->view = new \Sky4\View();
			$this->view->setBasicSubdirName($this->template_dir);
		}
		return $this->view;
	}

}
