<?php

namespace App\Classes;

class YandexMaps {

	public static function geocode($address) {
		$hash = md5($address);

		$fc = new \App\Model\FirmCoords();
		$fc->reader()
				->setWhere(['AND', '`hash` = :hash'], [':hash' => $hash])
				->objectByConds();
		if ($fc->exists()) {
			return [
				'lat' => $fc->val('coords_latitude'),
				'lng' => $fc->val('coords_longitude'),
				'id' => $fc->val('id')
			];
		} else {
			$params = [
                'apikey' => '98e409b3-3619-4172-94f6-aafe2d614206',
				'geocode' => $address, // адрес
				'format' => 'json', // формат ответа
				'results' => 1
			];

			$response = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($params, '', '&')));

			if (isset($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found) && $response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0) {
				$fc = new \App\Model\FirmCoords();
				$res = explode(' ', $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
				$fc->insert([
					'hash' => $hash,
					'coords_latitude' => $res[1],
					'coords_longitude' => $res[0]
				]);

				return [
					'lat' => $res[1],
					'lng' => $res[0]
				];
			} else {
				return [
					'lat' => 0,
					'lng' => 0
				];
			}
		}
	}

}
