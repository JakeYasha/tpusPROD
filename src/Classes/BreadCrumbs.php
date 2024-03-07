<?php

namespace App\Classes;

class BreadCrumbs extends \Sky4\Widget\BreadCrumbs {

	protected $elems = [];
	protected $elems_bottom = [];
	protected $separator = '';
	protected $view = null;

	public function __construct() {
		$this->separator = $this->defaultSeparator();
		return true;
	}

	public function getElems() {
		return $this->elems;
	}

	public function getElemsBottom() {
		return $this->elems_bottom;
	}

	public function removeElem($index) {
		if (isset($this->elems[$index])) {
			unset($this->elems[$index]);
		}

		$this->elems = array_values($this->elems);
		return $this;
	}

	public function getSeparator() {
		return $this->separator;
	}

	public function render($lk = false) {
		$view = $this->view()
						->set('bread_crumbs', $this)
						->set('elems', $this->getElems());
        if ($lk) {
            $view->setDirPath(APP_DIR_PATH.'/src/views2');
        }
        return $view->setTemplate('bread_crumbs')
						->render();
						
	}

	public function renderBottom() {
		return $this->view()
						->set('bread_crumbs', $this)
						->set('elems', $this->getElemsBottom())
						->setTemplate('bread_crumbs_bottom')
						->render();
	}

	public function renderElem($elem, $link_class = '') {
		$elem = (array) $elem;
		if (isset($elem['label']) && isset($elem['link']) && isset($elem['link_attrs']) && is_array($elem['link_attrs'])) {
			$link_class = (string) $link_class;
			if ($link_class) {
				if (isset($elem['link_attrs']['class'])) {
					$elem['link_attrs']['class'] .= ' ' . $link_class;
				} else {
					$elem['link_attrs']['class'] = $link_class;
				}
			}
			return \Sky4\Helper\Html::link($elem['label'], $elem['link'], $elem['link_attrs']);
		}
		return '';
	}

	/**
	 * @return \App\Classes\BreadCrumbs
	 */
	public function setElem($label, $link = '#', $link_attrs = array()) {
		$this->elems[] = array(
			'label' => (string) $label,
			'link' => (string) $link,
			'link_attrs' => (array) $link_attrs
		);
		return $this;
	}

	/**
	 * @return \App\Classes\BreadCrumbs
	 */
	public function setElemBottom($label, $link = '#', $link_attrs = array()) {
		$this->elems_bottom[] = array(
			'label' => (string) $label,
			'link' => (string) $link,
			'link_attrs' => (array) $link_attrs
		);
		return $this;
	}

	/**
	 * @return \App\Classes\BreadCrumbs
	 */
	public function setSeparator($separator) {
		$this->separator = (string) $separator;
		return $this;
	}

	/**
	 * @return \Sky4\View
	 */
	public function view() {
		if ($this->view === null) {
			$this->view = new \Sky4\View();
			$this->view->setBasicSubdirName('elem');
		}
		return $this->view;
	}

	protected function defaultSeparator() {
		return '&rarr;';
	}

}
