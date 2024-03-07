<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogRubricator;
use function app;

class Catalog extends Controller {

	public function renderRubrics($id_rubric = null, $firm_catalog = null, $mode = 'default', $rubrics_template = 'rubrics') {

		$result = '';
		//проверяем на наличие необходимого количества товаров или фирм
		if (app()->location()->stats('count_goods') > 75000 || (APP_IS_DEV_MODE && app()->location()->currentId() == '64043') || app()->location()->currentId() == '37000') {
			$pcr = new PriceCatalogRubricator();
			$where = ['AND', '`link` = :link', '`parent_node` = :parent_node'];
			if ($mode === 'index') {
				$params = [':link' => 'index', ':parent_node' => 0];
			} else {
				if ($firm_catalog === null) {
					$params = [':link' => $id_rubric === null ? '/' : '/' . (int) $id_rubric, ':parent_node' => 0];
				} else {
					$params = [':link' => '/firm/catalog/', ':parent_node' => 0];
				}
			}

			$pcr->reader()
					->setWhere($where, $params)
					->objectByConds();

			$rubrics = $pcr->adjacencyListComponent()->getChildren(null, null, ['AND', 'flag_is_active = :1'], '`position_weight` DESC, `id` ASC', null, null, [':1' => 1]);
			if (!$rubrics) {
				$params = [':link' => '/', ':parent_node' => 0];
				$pcr->reader()
						->setWhere($where, $params)
						->objectByConds();
				$rubrics = $pcr->adjacencyListComponent()->getChildren(null, null, ['AND', 'flag_is_active = :1'], '`position_weight` DESC, `id` ASC', null, null, [':1' => 1]);
			}
			$i = 0;
			$items = [];
			foreach ($rubrics as $rubric) {
				$items_blocks = $rubric->adjacencyListComponent()->getChildren(null, null, ['AND', 'flag_is_active = :1'], '`position_weight` DESC, `id` ASC', null, null, [':1' => 1]);
				foreach ($items_blocks as $block) {
					$items[$i][$block->id()]['item'] = $block;
					$items[$i][$block->id()]['subs'] = [];
					$children = $block->adjacencyListComponent()->getChildren(null, null, ['AND', 'flag_is_active = :1'], '`position_weight` DESC, `id` ASC', null, null, [':1' => 1]);
					foreach ($children as $child) {
						$items[$i][$block->id()]['subs'][] = $child;
					}
				}
				$i++;
			}

			$result = $this->view()
					->set('rubrics', $rubrics)
					->set('items', $items)
					->setTemplate($rubrics_template)
					->render();
		} else {
			$result = $this->view()
					->setTemplate('no_rubrics')
					->render();
        }

		return $result;
	}
    
    public function renderMobileRubrics($id_rubric = null, $firm_catalog = null, $mode = 'default', $rubrics_template = 'mobile_rubrics') {
        return $this->renderRubrics($id_rubric, $firm_catalog, $mode, $rubrics_template);
    }

	/**
	 * 
	 * @return PriceCatalog
	 */
	public function model() {
		if ($this->model === null) {
			$this->model = new PriceCatalog();
		}
		return $this->model;
	}

}
