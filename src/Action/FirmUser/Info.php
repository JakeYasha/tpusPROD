<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Model\DraftFirm;
use App\Model\DraftFirm\UserForm as DraftFirmUserForm;
use App\Model\FirmCoords;
use App\Model\FirmCoords\UserForm as FirmCoordsFirmUserForm;
use App\Model\FirmDelivery;
use App\Model\FirmDelivery\UserForm as FirmDeliveryFirmUserForm;
use App\Model\FirmDescription;
use App\Model\FirmDescription\UserForm as FirmDescriptionFirmUserForm;
use App\Model\Messengers\UserForm as MessengersForm;
use App\Model\FirmFile;
use const APP_IS_DEV_MODE;
use function app;

class Info extends FirmUser {

	public function execute() {
		app()->metadata()->setTitle('Личный кабинет - информация о фирме');

		$base_url = '/firm-user/info/';
		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string']
		]);
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Информация о фирме', $base_url);

		if (app()->firmManager()->exists()) {
			$tabs = [
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => false]), 'label' => 'Общая информация', 'mode' => false],
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'description']), 'label' => 'Описание и фотографии', 'mode' => 'description'],
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'files']), 'label' => 'Загрузка файлов', 'mode' => 'files'],
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'delivery']), 'label' => 'Доставка', 'mode' => 'delivery'],
                ['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'map']), 'label' => 'Схема проезда', 'mode' => 'map'],
                ['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'messengers']), 'label' => 'Чаты/Соцсети', 'mode' => 'messengers']
			];

		} else {
			$tabs = [
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => false]), 'label' => 'Общая информация', 'mode' => false],
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'description']), 'label' => 'Описание и фотографии', 'mode' => 'description'],
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'files']), 'label' => 'Загрузка файлов', 'mode' => 'files'],
				['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'delivery']), 'label' => 'Доставка', 'mode' => 'delivery'],
			];
		}

		$content = '';
		switch ($this->params['mode']) {
			case 'description' :
				$content = $this->renderFirmDescription();
				break;
			case 'files' :
				$content = $this->renderFirmFiles();
				break;
			case 'delivery' :
				$content = $this->renderFirmDelivery();
				break;
			case 'map' :
				$content = $this->renderFirmCoords();
				break;
			case 'messengers' :
				$content = $this->renderFirmMessengers();
				break;
			default : $content = $this->renderFirmCommon();
				break;
		}

		app()->tabs()
				->setLink('/firm-user/adv/')
				->setTabs($tabs)
				->setActiveTabByMode($this->params['mode'])
				->setFilters($this->params)
				->setActiveGroupOption(0);

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('content', $content)
				->set('tabs', app()->tabs()->render(null, true))
				->setTemplate('info_index')
				->save();
	}

	public function renderFirmDescription() {
		$success = app()->request()->processGetParams(['success' => 'string']);

		$firm_description = new FirmDescription();
		$firm_description->getByFirm($this->firm());
		$form = new FirmDescriptionFirmUserForm($firm_description);

		if (!$firm_description->exists()) {
			$form->setVals(['id_firm' => $this->firm()->id(), 'id_service' => $this->firm()->id_service()]);
		}

		return $this->view()
						->set('success_message', $success !== null)
						->set('form', $form->render($this->firm()))
						->setTemplate('info_description')
						->render();
	}

	public function renderFirmCommon() {
		$draft = new DraftFirm();
		$draft->getByFirm($this->firm());

		if (!$draft->exists()) {
			$vals = $this->firm()->getVals();
            $vals['id_firm'] = $this->firm()->id();
			$draft->setVals($vals);
		}

		$form = new DraftFirmUserForm($draft, $this->firm());

		return $this->view()
						->set('form', $form->render())
						->setTemplate('info_common')
						->render();
	}

	public function renderFirmFiles() {
		$firm = $this->firm();

		$ff = new FirmFile();
		$files = $ff->reader()
				->setWhere(['AND', '`id_firm` = :id_firm', 'type = :type'], [':id_firm' => $firm->id(), ':type' => 'file'])
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		return $this->view()
						->set('files', $files)
						->set('id_firm', $firm->id())
						->set('id_service', $firm->id_service())
						->setTemplate('info_files')
						->render();
	}

	public function renderFirmDelivery() {
		$success = app()->request()->processGetParams(['success' => 'string']);

		$firm_delivery = new FirmDelivery();
		$firm_delivery->getByFirm($this->firm());
		$form = new FirmDeliveryFirmUserForm($firm_delivery);

		if (!$firm_delivery->exists()) {
			$form->setVals(['id_firm' => $this->firm()->id(), 'id_service' => $this->firm()->id_service()]);
		}

		return $this->view()
						->set('success_message', $success !== null)
						->set('form', $form->render($this->firm()))
						->setTemplate('info_delivery')
						->render();
	}

	public function renderFirmCoords() {
		$success = app()->request()->processGetParams(['success' => ['type' => 'string']]);

		$hash = md5($this->firm()->address());

		$firm_coords = new FirmCoords();
		$firm_coords->reader()
				->setWhere(['AND', '`hash` = :hash'], [':hash' => $hash])
				->objectByConds();

		$form = new FirmCoordsFirmUserForm($firm_coords);
		app()->setUseMap(true);

		return $this->view()
						->set('success_message', $success !== null)
						->set('form', $form->render($this->firm(), $firm_coords))
						->setTemplate('info_firm_coords')
						->render();
	}
    
    public function renderFirmMessengers() {
        $success = app()->request()->processGetParams(['success' => 'string']);
		$form = new MessengersForm(null, $this->firm());
        
        $form->setVals([
            'id_firm' => $this->firm()->id(),
            'company_viber' => $this->firm()->val('viber'),
            'company_viber' => $this->firm()->val('company_viber'),
            'company_whatsapp' => $this->firm()->val('company_whatsapp'),
            'company_skype' => $this->firm()->val('company_skype'),
            'company_telegram' => $this->firm()->val('company_telegram'),
            'company_vk' => $this->firm()->val('company_vk'),
            'company_fb' => $this->firm()->val('company_fb'),
            'company_in' => $this->firm()->val('company_in'),
        ]);

		return $this->view()
                        ->set('success_message', $success !== null)
						->set('form', $form->render($this->firm()))
						->setTemplate('info_messengers')
						->render();
	}

}
