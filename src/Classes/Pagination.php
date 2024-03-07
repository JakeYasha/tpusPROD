<?php

namespace App\Classes;

class Pagination extends \Sky4\Widget\Pagination {

	protected $totals = [];
    protected $old_style = null;

	public function addNextElemToElems() {
		return true;
	}

	public function addPrevElemToElems() {
		return true;
	}

	public function getNextElemLabel($page) {
        if ($this->old_style == null) {
            return '&rarr;';
        } else {
            return '<svg width="6" height="10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.606.23a.838.838 0 011.155 0 .768.768 0 010 1.115L1.97 5l3.79 3.655a.768.768 0 010 1.114.838.838 0 01-1.155 0L.239 5.557a.768.768 0 010-1.114L4.606.23z" fill="#000"></path></svg>';
        }
	}

	public function getNextElemClass() {
		return '';
	}

	public function getPrevElemClass() {
		return '';
	}

	public function getCurrElemClass() {
		return 'cms_tree_current';
	}

	public function getPrevElemLabel($page) {
        if ($this->old_style == null) {
            return '&larr;';
        } else {
            return '<svg width="6" height="10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.606.23a.838.838 0 011.155 0 .768.768 0 010 1.115L1.97 5l3.79 3.655a.768.768 0 010 1.114.838.838 0 01-1.155 0L.239 5.557a.768.768 0 010-1.114L4.606.23z" fill="#000"></path></svg>';
        }
	}
    
    public function render($lk = false) {
        $this->old_style = app()->isNewTheme() ? false : true;
		$view = $this->view()
						->set('pagination', $this);
        
        if ($this->old_style) {
            $view->setDirPath(APP_DIR_PATH.'/src/views2');
        } else {
            $view->setDirPath(APP_DIR_PATH.'/src/views3');
        }
        
        return $view->setTemplate($this->getTemplate())
						->renderTemplate();
	}

	public function renderElem($page, $link, $label, $class = null) {
		$current_page = false;
		$_attrs = array();
		if (is_string($class)) {
			if (strpos(htmlspecialchars($class), $this->getCurrElemClass()) !== false) {
				$current_page = true;
			}
            if ($current_page) {
                $_attrs['class'] = htmlspecialchars($class) . ' pagination__item pagination__item_active' . ($this->old_style ? ' old' : ' new');
            } else {
                $_attrs['class'] = htmlspecialchars($class) . ' pagination__item';
            }
        } else {
            $_attrs['class'] = ' pagination__item';
        }
		$attrs = $current_page == false ? array('href' => (string) $link, 'class' => 'pagination__link') : array('class' => 'current_page pagination__link');


		return $current_page == false ? '<li' . \CHtml::renderAttrs($_attrs) . '><a' . \CHtml::renderAttrs($attrs) . '>' . (string) $label . '</a></li>' :
				'<li' . \CHtml::renderAttrs($_attrs) . '><span' . \CHtml::renderAttrs($attrs) . '>' . (string) $label . '</span></li>';
	}

	public function calculateParams() {
		if ($this->getTotalRecordsLimit() && ($this->getTotalRecords() > $this->getTotalRecordsLimit())) {
			$this->setTotalRecords($this->getTotalRecordsLimit());
		}
		if ($this->getLimit()) {
			$this->setTotalPages(ceil($this->getTotalRecords() / $this->getLimit()));
		}
		if ($this->getPage() > $this->getTotalPages() && $this->getTotalPages() !== 1) {
			$this->setPage($this->getTotalPages());
		}
		if ($this->getPage() == 1) {
			$this->setOffset(0);
		} else {
			$this->setOffset(($this->getPage() - 1) * $this->getLimit());
		}
		$left_page = $this->getPage() - $this->getNearbyPages();
		$right_page = $this->getPage() + $this->getNearbyPages();
		if ($this->getLeftPageOffset()) {
			$left_page = $this->getPage();
			$left_page_with_offset = $this->getLeftPageOffset() - $this->getNearbyPages();
			if ($left_page_with_offset < 1) {
				$right_page += abs($left_page_with_offset) + 1;
				if ($right_page > $this->getTotalPages()) {
					$right_page = $this->getTotalPages();
				}
			}
			if ($right_page > $this->getTotalPages()) {
				$left_page_with_offset -= abs($this->getTotalPages() - $right_page);
				if ($left_page_with_offset < 1) {
					$left_page_with_offset = 1;
				}
				$right_page = $this->getTotalPages();
			}
			$this->setLeftPage($left_page)
					->setLeftPageWithOffset($left_page_with_offset)
					->setRightPage($right_page);
		} else {
			if ($left_page < 1) {
				$right_page += abs($left_page) + 1;
				if ($right_page > $this->getTotalPages()) {
					$right_page = $this->getTotalPages();
				}
				$left_page = 1;
			}
			if ($right_page > $this->getTotalPages()) {
				$left_page -= abs($this->getTotalPages() - $right_page);
				if ($left_page < 1) {
					$left_page = 1;
				}
				$right_page = $this->getTotalPages();
			}
			$this->setLeftPage($left_page)
					->setRightPage($right_page);
		}

		return $this;
	}

	public function setTotalRecordsParam($param_name, $val) {
		$this->totals[$param_name] = $val;
		return $this;
	}

	public function getTotalRecordsParam($param_name) {
		return isset($this->totals[$param_name]) ? intval($this->totals[$param_name]) : 0;
	}

	public function genLink($page) {
		$page = ((int) $page > 0) ? (int) $page : 1;
		$result = '';
		if ($page == 1) {
			$result = $this->getBasicLink() ? $this->getBasicLink() : $this->getLink();
		} else {
			$result = $this->getLink();
		}

		if ($this->getLinkPostfix()) {
			$result .= $this->getLinkPostfix() . '/';
		}
		$link_params = $this->getLinkParams();
		if ($page != 1) $link_params['page'] = $page;

		if (!empty($link_params)) {
			$_link_params = [];
			foreach ($link_params as $link_param_name => $link_param_val) {
				if ($link_param_val || $link_param_name === 'q') {
					$_link_params[] = urlencode((string) $link_param_name) . '=' . urlencode((string) $link_param_val);
				}
			}

			if ($_link_params) {
				$result .= '?' . implode('&', $_link_params);
			}
		}
		return $result;
	}

	public function setLinkParams($params) {
		if (isset($params['page'])) {
			unset($params['page']);
		}
		$this->link_params = (array) $params;
		return $this;
	}

}
