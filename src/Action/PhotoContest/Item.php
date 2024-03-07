<?php

namespace App\Action\PhotoContest;

class Item extends \App\Action\PhotoContest {

	public function execute($id = 0) {
		$pci = new \App\Model\PhotoContestItem();
		$this->findModelObject($id);

		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string'],
			'nomination' => ['type' => 'int'],
			'success_add' => ['type' => 'int']
		]);

		app()->breadCrumbs()
				->setElem('Фото-конкурсы', '/photo-contest/')
				->setElem($this->model()->name(), $this->model()->link());

		if (isset($this->params['nomination']) && $this->model()->exists()) {
			app()->metadata()->setCanonicalUrl(app()->link('/photo-contest/item/' . $id . '/'));
		}

		$url = $this->model()->link();

		$tabs = [
			['link' => app()->linkFilter($url, ['mode' => null]), 'label' => 'Номинации конкурса', 'mode' => false],
			['link' => app()->linkFilter($url, ['mode' => 'rights']), 'label' => 'Правила голосования', 'mode' => 'rights'],
			['link' => app()->linkFilter($url, ['mode' => 'terms']), 'label' => 'Условия конкурса', 'mode' => 'terms'],
			['link' => app()->linkFilter($url, ['mode' => 'prizes']), 'label' => 'Призы', 'mode' => 'prizes'],
		];

		app()->tabs()
				->setLink('/firm-user/adv/')
				->setTabs($tabs)
				->setActiveTabByMode($this->params['mode'])
				->setFilters($this->params)
				->setActiveGroupOption(0);

		app()->metadata()->setFromModel($this->model());
		$metadata_title = app()->metadata()->getTitle();
		switch ($this->params['mode']) {
			case 'rights' : $tab_text = $this->model()->val('text_rights');
				app()->metadata()
						->setTitle($metadata_title . ' - правила голосования')
						->setMetatag('description', $metadata_title . ' - читай правила, голосуй за фотографию и помоги выбрать лучшего участника')
						->setMetatag('keywords', $metadata_title . ', правила голосования');
				break;
			case 'terms' : $tab_text = $this->model()->val('text_terms');
				app()->metadata()
						->setTitle($metadata_title . ' - условия конкурса')
						->setMetatag('description', $metadata_title . ' - читай условия участия в конкурсе, размещай свою фотографию и выиграй приз')
						->setMetatag('keywords', $metadata_title . ', условия участия в конкурсе');
				break;
			case 'prizes' : $tab_text = $this->model()->val('text_prizes');
				app()->metadata()
						->setTitle($metadata_title . ' - призы конкурса')
						->setMetatag('description', $metadata_title . ' - призы для конкурса предоставлены нашими партнерами, участвуй и выиграй свой приз')
						->setMetatag('keywords', $metadata_title . ', призы для участников конкурса');
				break;
			default : $tab_text = '';
		}

		$nominations = $this->model()->getNominations($this->params);
		$active_nomination_id = 0;
		$active_nomination_name = '';
		foreach ($nominations as $nom) {
			if ($nom['active']) {
				$active_nomination_id = $nom['id'];
				$active_nomination_name = $nom['name'];
			}
		}

		$item = $this->model()->prepare();

		$winners = [];
		if ($item['finished'] && $item['has_winner']) {
			$pci = new \App\Model\PhotoContestItem();
			$winners = $pci->getWinnersList($this->model()->id(), $nominations);
		}

		return $this->view()
						->set('active_nomination_name', $active_nomination_name)
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('filters', $this->params)
						->set('item', $item)
						->set('photos', $pci->getActiveNominationItemsList($active_nomination_id, $this->model()->id()))
						->set('nominations', $nominations)
						->set('tab_text', $tab_text)
						->set('tabs', app()->tabs()->render())
						->set('winners', $winners)
						->setTemplate('item')
						->save();
	}

}
