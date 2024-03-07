<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\Firm;
use App\Model\FirmUser as FirmUserModel;
use Sky4\Utils;
use function app;
use function str;

class FirmUser extends Controller {

	public function renderSidebar() {
		$counts = [];
		$counts['review'] = $this->getItemsCount('firm-review');
		$counts['request'] = $this->getItemsCount('price-request');
		$counts['feedback'] = $this->getItemsCount('firm-feedback');
		if (app()->firmUser()->id_service() == 10) {
			$menu = [
				['link' => self::link('/profile/'), 'name' => 'Профиль', 'count' => null, 'first' => true],
				['link' => self::link('/info/'), 'name' => 'Информация о фирме', 'count' => null],
                ['link' => self::link('/firm-branch/'), 'name' => 'Филиалы фирмы', 'count' => null],
                ['link' => self::link('/price/'), 'name' => 'Товары и услуги', 'count' => null],
				['link' => self::link('/promo/'), 'name' => 'Акции и скидки', 'count' => null],
				['link' => self::link('/advert-module/'), 'name' => 'Рекламные модули', 'count' => null],
				['link' => self::link('/video/'), 'name' => 'Видеоблог', 'count' => null, 'last' => true],
				//
				['link' => self::link('/review/'), 'name' => 'Отзывы', 'count' => $counts['review'], 'first' => true],
				['link' => self::link('/request/'), 'name' => 'Заказы', 'count' => $counts['request']],
				['link' => self::link('/feedback/'), 'name' => 'Сообщения', 'count' => $counts['feedback'], 'last' => true],
				//
				['link' => self::link('/statistics/'), 'name' => 'Статистика tovaryplus.ru', 'count' => null, 'first' => true],
                ['link' => self::link('/adv/'), 'name' => 'Баннеры tovaryplus.ru', 'count' => null],
				['link' => self::link('/online-statistics/'), 'name' => 'Статистика 727373.ru', 'count' => null],
				['link' => self::link('/adv-online/'), 'name' => 'Баннеры 727373.ru', 'count' => null],
				['link' => self::link('/calls/'), 'name' => 'Статистика звонков', 'count' => null],
				['link' => self::link('/export/'), 'name' => 'Статистика email', 'count' => null, 'last' => true],
				//
				['link' => self::link('/reports/'), 'name' => 'Отчеты', 'count' => null, 'first' => true],
			];
		} else {
			$menu = [
				['link' => self::link('/profile/'), 'name' => 'Профиль', 'count' => null, 'first' => true],
				['link' => self::link('/info/'), 'name' => 'Информация о фирме', 'count' => null],
                ['link' => self::link('/firm-branch/'), 'name' => 'Филиалы фирмы', 'count' => null],
				['link' => self::link('/price/'), 'name' => 'Прайс-лист', 'count' => null],
				['link' => self::link('/promo/'), 'name' => 'Акции и скидки', 'count' => null],
				['link' => self::link('/advert-module/'), 'name' => 'Рекламные модули', 'count' => null],
				['link' => self::link('/video/'), 'name' => 'Видеоблог', 'count' => null, 'last' => true],
				//
				['link' => self::link('/review/'), 'name' => 'Отзывы', 'count' => $counts['review'], 'first' => true],
				['link' => self::link('/request/'), 'name' => 'Заказы', 'count' => $counts['request']],
				['link' => self::link('/feedback/'), 'name' => 'Сообщения', 'count' => $counts['feedback'], 'last' => true],
				//
				['link' => self::link('/statistics/'), 'name' => 'Статистика tovaryplus.ru', 'count' => null, 'first' => true],
				['link' => self::link('/adv/'), 'name' => 'Статистика баннеров', 'count' => null],
				['link' => self::link('/calls/'), 'name' => 'Статистика звонков', 'count' => null],
				['link' => self::link('/export/'), 'name' => 'Статистика email', 'count' => null, 'last' => true],
				//
				['link' => self::link('/reports/'), 'name' => 'Отчеты', 'count' => null, 'first' => true],
			];
		}

		$uri = app()->request()->getRequestUri();
		foreach ($menu as $k => $v) {
			if (str()->pos($uri, $v['link']) !== false) {
				$menu[$k]['active'] = true;
			} else {
				$menu[$k]['active'] = false;
			}
		}

		//выбираем фирмы с тем-же email пользователя
		$firms = [];
		if (!(app()->firmManager()->exists() && app()->firmManager()->isSuperMan())) {
			$firm_user = new FirmUserModel();
			$firm_users = $firm_user->reader()
					->setWhere(['OR', ['AND', 'email = :email', 'email != :empty'], ['AND', 'id_firm = :id_firm']], [':email' => app()->firmUser()->val('email'), ':empty' => '', ':id_firm' => app()->firmUser()->id_firm()])
					->objects();

			foreach ($firm_users as $fu) {
				$firm = new Firm();
				$firm->getByIdFirm($fu->id_firm());
				if ($firm->exists() && !$firm->isBlocked()) {
					$firms[] = ['name' => $firm->name(), 'link' => $firm->link(), 'id_user' => $fu->id(), 'active' => $firm->id_firm() === app()->firmUser()->id_firm()];
				}
			}
		} else {
			$firms[] = ['name' => $this->firm()->name(), 'link' => $this->firm()->link(), 'id_user' => 0, 'active' => true];
		}
		//

		return $this->view()
						->set('menu', $menu)
						->set('firm', $this->firm())
						->set('firms', $firms)
						->setTemplate('sidebar')
						->render();
	}

	protected function getItemsCount($model_alias) {
		$object = Utils::getModelClass($model_alias);

		$counts = [];

		$counts['new'] = (int) $object->reader()
						->setSelect(['id'])
						->setWhere(['AND', 'id_firm = :id_firm', 'timestamp_inserting > :last_act_timestamp'], [':last_act_timestamp' => app()->firmUserTimestamp()->getTimestampByModel($object), ':id_firm' => app()->firmUser()->id_firm()])
						->count();

		$counts['all'] = (int) $object->reader()
						->setSelect(['id'])
						->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => app()->firmUser()->id_firm()])
						->count();

		return $counts;
	}

	public static function link($link, $filters = [], $current = []) {
		$action = new \App\Action\FirmUser();
		return $action->link($link, $filters, $current);
	}

	public function firm() {
		$action = new \App\Action\FirmUser();
		return $action->firm();
	}

}
