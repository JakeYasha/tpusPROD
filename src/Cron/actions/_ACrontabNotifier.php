<?php

class ACrontabNotifier extends ACrontabAction {

	public function run() {
		$this->log('отправляем сообщения менеджерам о баннерах');
		$this->sendBannersMessages();
		$this->log('отправляем сообщения менеджерам о рекламных модулях');
		$this->sendAdvertModulesMessages();
		$this->log('завершено');
	}

	private function sendBannersMessages() {
		$banner = new Banner();

		$start = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m'), date('d') + 5));
		$end = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(23, 59, 59, date('m'), date('d') + 5));

		$items = $banner->setWhere(['AND', 'timestamp_ending >= :start', 'timestamp_ending < :end', 'flag_is_active = :flag_is_active'], [':start' => $start, ':end' => $end, ':flag_is_active' => 1])
				->getAll();

		foreach ($items as $it) {
			$firm = new Firm();
			$firm->getByIdFirm($it->id_firm());
                        
			if ($it->id_service() == 10) {
                                $manager = new FirmManager();
                                $manager->getByFirm($firm);
                                if ($manager->exists()) {
                                        $this->sendBannerNoticeToManager($manager, $it);
                                }
                        } else {
                                $service = new StsService();
                                $service->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
					->getByConds();
                                if ($service->exists()) {
                                        $this->sendBannerNoticeToStsService($service, $it);
                                }
                        }
		}

		return $this;
	}

	private function sendBannerNoticeToManager(FirmManager $manager, Banner $banner) {
		App::email()
				->setSubject('Заканчивается время размещения баннера')
				->setTo($manager->val('email'))
				->setModel($banner)
				->setTemplate('email_banner_notice', 'firmmanager')
				->sendToQuery();

		return $this;
	}
        
        private function sendBannerNoticeToStsService(StsService $service, Banner $banner) {
		App::email()
				->setSubject('Заканчивается время размещения баннера')
				->setTo($service->val('email'))
				->setModel($banner)
				->setTemplate('email_banner_notice', 'firmmanager')
				->sendToQuery();

		return $this;
	}
        
        private function sendAdvertModulesMessages() {
		$advert_module = new AdvertModule();

		$start = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m'), date('d') + 7));
		$end = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(23, 59, 59, date('m'), date('d') + 7));

		$items = $advert_module->setWhere(['AND', 'timestamp_ending >= :start', 'timestamp_ending < :end', 'flag_is_active = :flag_is_active'], [':start' => $start, ':end' => $end, ':flag_is_active' => 1])
				->getAll();

		foreach ($items as $it) {
			$firm = new Firm();
			$firm->getByIdFirm($it->id_firm());
                        
			if ($it->id_service() == 10) {
                                $manager = new FirmManager();
                                $manager->getByFirm($firm);
                                if ($manager->exists()) {
                                        $this->sendAdvertModuleNoticeToManager($manager, $it);
                                }
                        } else {
                                $service = new StsService();
                                $service->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
					->getByConds();
                                if ($service->exists()) {
                                        $this->sendAdvertModuleNoticeToStsService($service, $it);
                                }
                        }
		}

		return $this;
	}
        
        private function sendAdvertModuleNoticeToManager(FirmManager $manager, AdvertModule $advert_module) {
		App::email()
				->setSubject('Заканчивается время размещения рекламного модуля')
				->setTo($manager->val('email'))
				->setModel($advert_module)
				->setTemplate('email_advert_module_notice', 'firmmanager')
				->sendToQuery();

		return $this;
	}
        
        private function sendAdvertModuleNoticeToStsService(StsService $service, AdvertModule $advert_module) {
		App::email()
				->setSubject('Заканчивается время размещения рекламного модуля')
				->setTo($service->val('email'))
				->setModel($advert_module)
				->setTemplate('email_advert_module_notice', 'firmmanager')
				->sendToQuery();

		return $this;
	}


}
