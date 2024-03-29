<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Presenter\FirmUserStatistics;
use function app;

class Calls extends FirmUser {

	public function execute() {
		$filters = $this->getFilters();
		$this->text()->getByLink('firm-user/calls');
		if (!$this->text()->getVal('title')) {
			$this->text()->setVal('metadata_title', 'Личный кабинет - статистика звонков');
		}
		app()->metadata()->setFromModel($this->text());

		$url = '/calls/';
		$base_url = self::link($url);
		if ($filters === null) {
			$this->params = self::initFilters(app()->request()->processGetParams([
								'mode' => 'string',
								't_start' => 'int',
								't_end' => 'int',
								'group' => 'string',
								'page' => 'int',
			]));
		} else {
			$this->params = $filters;
		}

		app()->breadCrumbs()
				->setElem('Статистика звонков', $base_url);

		$presenter = new FirmUserStatistics();
		$presenter->setLimit($this->isHtmlMode() ? 99999 : 15);
		$presenter->findCalls($this->params, app()->firmUser()->firm()->val('id_service') == 10 ? false : true);

		if ($this->isHtmlMode()) {
			return $presenter->renderItems();
		}

		list($dates_block, $visible) = self::getDatesBlock($url, $this->params);

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('dates_block', $dates_block)
				->set('items', $presenter->renderItems())
                ->set('pagination', $presenter->pagination()->render(true))
				->setTemplate('calls_index')
				->save();
	}

}
