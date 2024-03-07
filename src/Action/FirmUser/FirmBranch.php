<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Model\FirmBranch\UserForm as FirmBranchUserForm;
use App\Model\FirmBranch\FormAdd as FirmBranchAddForm;
use const APP_IS_DEV_MODE;
use function app;

class FirmBranch extends FirmUser {

	public function execute($id_firm_branch = null) {
		app()->metadata()->setTitle('Личный кабинет - филиалы фирмы');

		$base_url = '/firm-user/firm-branch/';

		$content = '';
		if ($id_firm_branch === null) {
            app()->breadCrumbs()
                    ->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
                    ->setElem('Филиалы фирмы', $base_url);
			$content = $this->renderFirmBranches();
        } else if ($id_firm_branch == 'add') {
            app()->breadCrumbs()
                    ->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
                    ->setElem('Филиалы фирмы', $base_url)
                    ->setElem('Новый филиал фирмы', $base_url);
            $content = $this->renderFirmBranchAdd();
		} else if ((int)$id_firm_branch > 0) {
            app()->breadCrumbs()
                    ->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
                    ->setElem('Филиалы фирмы', $base_url)
                    ->setElem('Филиал фирмы', $base_url);
            $content = $this->renderFirmBranch($id_firm_branch);
        }

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('content', $content)
				->setTemplate('firm_branches_index')
				->save();
	}

	public function renderFirmBranches() {
		$_fb = new \App\Model\FirmBranch();
        $firm_branches = $_fb->reader()
                ->setWhere(
                        ['AND', 'firm_id = :firm_id'],
                        [':firm_id' => $this->firm()->id()]
                    )
                ->setOrderBy('id_region_country, id_city')
                ->objects();

		return $this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('filters', $this->params)
				->set('text', $this->text())
				->set('items', $firm_branches)
				->setTemplate('firm_branch')
				->render();
	}

    public function renderFirmBranch($id_firm_branch) {
		$_fb = new \App\Model\FirmBranch();
        $firm_branch = $_fb->reader()
                ->setWhere([
                        'AND',
                        'firm_id = :firm_id',
                        'id = :firm_branch'
                    ],[
                        ':firm_id' => $this->firm()->id(), 
                        ':firm_branch' => (int)$id_firm_branch
                    ])
                ->objectByConds();

        if (!$firm_branch->exists() || $firm_branch->isBlocked()){
            app()->response()->redirect('/firm-user/firm-branch/');
        }

        $form = new FirmBranchUserForm($firm_branch, $this->firm());        
        $title = 'Филиал #' . $firm_branch->id() . ' ' . $firm_branch->name() . ' по адресу ' . $firm_branch->address();
		return $this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('form', $form->render())
				->set('filters', $this->params)
				->set('title', $title)
				->set('text', $this->text())
				->set('item', $firm_branch)
				->setTemplate('firm_branch')
				->render();
	}
    
    public function renderFirmBranchAdd() {
		$firm_branch = new \App\Model\FirmBranch();

        $_vals = $this->firm->getVals();
        $firm_branch->setVals($_vals);
        $form = new FirmBranchAddForm($firm_branch, $this->firm());
        $title = "Новый филиал фирмы";
		return $this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('form', $form->render())
				->set('filters', $this->params)
				->set('title', $title)
				->set('text', $this->text())
				->set('item', $firm_branch)
				->setTemplate('firm_branch')
				->render();
	}
}
