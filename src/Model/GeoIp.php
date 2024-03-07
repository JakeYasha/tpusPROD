<?php

namespace App\Model;
use CFile;

class GeoIp extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\CoordsTrait,
	 Component\IpAddrTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		return [
			'country' => [
				'elem' => 'text_field',
				'label' => 'Страна'
			],
			'district' => [
				'elem' => 'text_field',
				'label' => 'Район'
			],
			'ip_addr' => [
				'elem' => 'text_field',
				'label' => 'IP-адрес'
			],
			'region' => [
				'elem' => 'text_field',
				'label' => 'Регион'
			],
			'town' => [
				'elem' => 'text_field',
				'label' => 'Город'
			]
		];
	}

	// -------------------------------------------------------------------------

	public function getFromCache($ip_addr = null) {
		$result = [
			'coords_latitude' => '',
			'coords_longitude' => '',
			'country' => '',
			'district' => '',
			'ip_addr' => '',
			'region' => '',
			'town' => ''
		];
		if ($ip_addr === null) {
			$ip_addr = app()->request()->getRemoteAddr();
		}
        $user_agent_yand = $_SERVER["HTTP_USER_AGENT"];
        if (strpos($user_agent_yand, "Yandex") !== false) $ip_addr = '93.158.228.86';
                
		if ($ip_addr !== null) {
			$this->reader()
					->setWhere('`ip_addr` = :ip_addr', [':ip_addr' => $ip_addr])
					->objectByConds();
//для роботов игнорить ip и ставить ярославль User-agent: YandexMobileBot User-agent: YandexCalendar
			if (!$this->exists()) {
				$file = new CFile();
                
                
                
                
                
				$data = json_decode(json_encode(simplexml_load_string($file->getRemoteFile('http://ipgeobase.ru:7020/geo?ip=' . $ip_addr))), true);
				if (is_array($data) && isset($data['ip']) && is_array($data['ip']) && isset($data['ip']['city']) && isset($data['ip']['country']) && isset($data['ip']['district']) && isset($data['ip']['lat']) && isset($data['ip']['lng']) && isset($data['ip']['region'])) {
					// @todo: Заменить на REPLACE INTO.
					$this->insert([
						'coords_latitude' => $data['ip']['lat'],
						'coords_longitude' => $data['ip']['lng'],
						'country' => $data['ip']['country'],
						'district' => $data['ip']['district'],
						'ip_addr' => $ip_addr,
						'region' => $data['ip']['region'],
						'town' => $data['ip']['city']
					]);
				}
			}
			$result['coords_latitude'] = $this->val('coords_latitude');
			$result['coords_longitude'] = $this->val('coords_longitude');
			$result['country'] = $this->val('country');
			$result['district'] = $this->val('district');
			$result['ip_addr'] = $ip_addr;
			$result['region'] = $this->val('region');
			$result['town'] = $this->val('town');
		}
		return $result;
	}

}
