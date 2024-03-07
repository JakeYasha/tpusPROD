<?php

namespace App\Model;

class YmlOffer extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\ImagesTrait,
	 Component\NameTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		$c = $this->fieldPropCreator();

		return [
			'status' => $c->radioButtons_typeList('Статус', $this->getStatuses()),
			'timestamp' => $c->dateTimeField('Дата обновления предложения'),
			'id_catalog' => $c->intField('ID каталога'),
			'id_yml_category' => $c->intField('ID категории из YML'),
			'id_yml_file' => $c->intField('ID файла YML'),
			'id_yml' => $c->intField('ID предложения из YML', 8),
			'currency' => $c->stringField('Валюта', 5),
			'country_of_origin' => $c->stringField('Страна', 20),
			'description' => $c->textArea_typeBig('Описание'),
			'price' => $c->priceField('Цена'),
			'old_price' => $c->priceField('Цена'),
			'url' => $c->stringField('Url товара', 1024),
			'vendor' => $c->stringField('Бренд или производитель'),
			'flag_is_available' => $c->singleCheckBox('В наличии'),
			'flag_is_ready' => $c->singleCheckBox('Готов к отображению'),
			'flag_is_delivery' => $c->singleCheckBox('Есть доставка?'),
			'flag_is_referral' => $c->singleCheckBox('Использовать рефферальную ссылку'),
		];
	}

	public static function getStatuses() {
		return [
			'' => 'Загружен',
			'confirmed' => 'Подтвержден',
			'deleted' => 'Удален'
		];
	}

	public function prepare($items) {
		$result = [];
		$statuses = $this->getStatuses();

		foreach ($items as $item) {
			$timestamp_inserting = new \Sky4\Helper\DateTime($item->val('timestamp_inserting'));
			$timestamp_yml = new \Sky4\Helper\DateTime($item->val('timestamp_yml'));

			$result[$item->id()] = [
				'id' => $item->id(),
				'status' => $statuses[$item->val('status')],
				'name' => $item->name(),
				'timestamp_inserting' => $timestamp_inserting->format('d.m.Y H:i:s'),
				'timestamp_yml' => $timestamp_yml->format('d.m.Y H:i:s'),
			];
		}

		return $result;
	}

	public function delete() {
		$images = \Sky4\Model\Utils::getObjectsByIds($this->val('images'));
		foreach ($images as $img) {
			$img->delete();
		}

		$yml_param = new YmlParam();
		$params = $yml_param->reader()->setWhere(['AND', 'id_yml_offer = :id_yml_offer'], [':id_yml_offer' => $this->id()])
				->objects();

		foreach ($params as $param) {
			$param->delete();
		}

		return parent::delete();
	}

}
