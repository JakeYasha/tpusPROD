<?php

namespace App\Action\FirmManager;

use App\Model\Issue as IssueModel;
use App\Presenter\IssueItems;
use function app;

class Issue extends \App\Action\FirmManager {

	public function execute() {
		$firm_manager_exists = app()->firmManager()->exists();
		app()->metadata()->setTitle('Кабинет редактора - выпуски');
		app()->breadCrumbs()
				->setElem('Выпуски', '/firm-manager/issues/');

		$this->params = app()->request()->processGetParams([
			'mode' => 'string',
			'id' => 'int'
		]);

		switch ($this->params['mode']) {
			case 'add' : $content = $this->getIssueAddForm();
				break;
			case 'edit' : $content = $this->getIssueEditForm();
				break;
			case 'delete' : $this->deleteIssue();
				break;
			default : $content = $this->getIssueIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('has_add_btn', $firm_manager_exists && $this->params['mode'] === null)
				->set('content', $content)
				->setTemplate('issue')
				->save();
	}

	private function getIssueAddForm() {
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-manager/issue/');
		}
		app()->breadCrumbs()
				->setElem('Добавить выпуск', '');

		$form = new \App\Model\Issue\ManagerForm();
		$form
				->setDefaultVals()
				->setVals(['id_service' => app()->firmManager()->id_service(), 'id_city' => app()->firmManager()->val('id_city')]);

		return $form->render();
	}

	private function getIssueEditForm() {
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-manager/issue/');
		}
		app()->breadCrumbs()
				->setElem('Изменить выпуск', '');
		$model = new IssueModel($this->params['id']);

		$form = new \App\Model\Issue\ManagerForm($model);
		return $form->render();
	}
 
	private function deleteIssue() {
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-manager/issue/');
		}
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$advert_module = new IssueModel($id);
		$this->delete($advert_module, '/firm-manager/issue/');
	}

	private function getIssueIndex() {
		$advert_module_presenter = new IssueItems();
		$advert_module_presenter->setItemsTemplateSubdirName('firmmanager');
		$advert_module_presenter->setLimit(20);
		$advert_module_presenter->find();

		return $this->view()
						->set('items', $advert_module_presenter->renderItems())
						->set('pagination', $advert_module_presenter->pagination()->render(true))
						->set('total_founded', $advert_module_presenter->pagination()->getTotalRecords())
						->setTemplate('presenter_issue_items')
						->render();
	}

}


/*                
    $_issue = new IssueModel();
    $last_issue = $_issue->reader()
            ->setSelect('MAX(number) as max_number')
            ->rowByConds();

    $last_issue_number = 0;
    if (isset($last_issue[0])) {
        $last_issue_number = $last_issue[0]['max_number'];
        $last_issue_number++;
    }
    $last_issue_number++;
*/