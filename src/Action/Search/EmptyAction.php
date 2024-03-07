<?php

namespace App\Action\Search;

class EmptyAction extends \App\Action\Search {

	public function execute() {
		app()->breadCrumbs()
				->setElem('Результаты поиска', app()->link('/search/'));

		$this->text()->getByLink('bad_search');
		$text = $this->text()->val('text');
		$text = str()->replace($text, '"%query"', '');
		$text = str()->replace($text, ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentFullId(), app()->location()->currentName()]);

		app()->metadata()->setTitle('По запросу ничего не найдено');

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				->setTemplate('empty', 'search')
				->save();

		return $this;
	}

}
