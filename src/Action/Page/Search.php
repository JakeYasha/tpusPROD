<?php

namespace App\Action\Page;

class Search extends \App\Action\Page {

	public function execute() {
		$params = app()->request()->processGetParams([
			'q' => ['type' => 'string']
		]);

		if ($params['q']) {
			$result = [
				['label' => 'Страницы', 'items' => $this->search('page', $params['q'])],
				['label' => 'Новости', 'items' => $this->search('news', $params['q'])]
			];


			$this->view()
					->setTemplate('search')
					->set('items', $result)
					->save();
		} else {
			throw new \Sky4\Exception();
		}

		return $this;
	}

	private function search($model_alias, $query) {
		$model = \Sky4\Utils::getModelClass($model_alias);
		$_where = [
			'AND',
			'`state` = :state',
			[
				'OR',
				'`name` LIKE :query',
				'`text` LIKE :query',
				'`brief_text` LIKE :query'
			]
		];
		$_params = [
			':state' => 'published',
			':query' => '%' . $query . '%'
		];

		return $model->reader()
						->setWhere($_where, $params)
						->objects();
	}

}
