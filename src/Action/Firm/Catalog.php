<?php

namespace App\Action\Firm;

use Sky4\Exception;
use App\Model\AdvText;

class Catalog extends \App\Action\Firm {

	public function execute($mode = 'catalog', $letter = null, $sub_letter = null) {
		if ($mode === 'catalog') $this->catalog();
		else throw new Exception();

		app()->tabs()->setTabs([
			['link' => app()->link('/firm/catalog/'), 'label' => 'По типу'],
			['link' => app()->link('/firm/catalog/alphabet/'), 'label' => 'По алфавиту']
		]);

        app()->frontController()->layout()->setTemplate('catalog');
                $adv_text = new AdvText();
                
		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('tabs', app()->tabs()->render())
				->set('mode', $mode)
                                ->set('position', $adv_text->getByUrl(app()->location()->linkPrefix() . app()->request()->getRequestUri()))
				->setTemplate('catalog')
				->save();
	}

}
