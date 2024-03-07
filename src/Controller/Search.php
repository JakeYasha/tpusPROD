<?php

namespace App\Controller;

class Search extends \App\Classes\Controller {

	public function actionEmpty() {
		$action = new \App\Action\Search\EmptyAction();
		return $action->execute();
		//naming fix
	}

//	public function renderFirmCatalogs($search_links = false) {
//		$result = '';
//		if ($this->firm_catalogs) {
//			$result = $this->view()
//					->set('search_links', $search_links)
//					->set('items', $this->firm_catalogs)
//					->set('matrix', $this->firm_catalogs_matrix)
//					->setTemplate('firm_catalogs', 'search')
//					->render();
//		}
//
//		return $result;
//	}
//
//	public function renderPriceCatalogs() {
//		$result = '';
//		if ($this->price_catalogs) {
//			$result = $this->view()
//					->set('items', $this->price_catalogs)
//					->set('matrix', $this->price_catalogs_matrix)
//					->setTemplate('price_catalogs', 'search')
//					->render();
//		}
//
//		return $result;
//	}

}
