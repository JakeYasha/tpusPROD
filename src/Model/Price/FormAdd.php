<?php

namespace App\Model\Price;

class FormAdd extends \Sky4\Model\Form {

	protected $mode;

	public function editableFieldsNames() {
		return array_keys($this->fields());
	}

	public function __construct($model = null, $params = []) {
		parent::__construct($model, $params);
		if ($model === null) {
			$this->setModel(new \App\Model\Price());
		}
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Сохранить',
				'attrs' => [
					'class' => 'send js-send btn btn_primary',
					'type' => 'submit',
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-user/price/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fc = $this->fieldPropCreator();

		if (isset($this->params['step']) && (int)$this->params['step'] !== 2) {
			$fields = [
				'id_catalog' => $fc->autocomplete('Начните вводить название предложения или рубрики каталога, куда оно должно попасть по вашему мнению и выберите из списка подсказок подходящую рубрику', 'price-catalog', 'id')
			];
		} else {
			$fields = [
				'id_catalog' => $fc->hiddenField('Выбранная рубрика'),
				'id_group' => $fc->hiddenField('ID_GROUP'),
				'id_subgroup' => $fc->hiddenField('ID_SUBGROUP'),
				'id' => $fc->hiddenField('ID'),
				'name' => $fc->stringField('Название', 200, ['rules' => ['length' => ['max' => 200, 'min' => 5], 'required']]),
				'description' => $fc->textArea('Описание', ['rules' => ['length' => ['max' => 1000, 'min' => 5]]]),
				'price' => $fc->doubleField('Розничная цена'),
				'price_wholesale' => $fc->doubleField('Оптовая цена'),
				'price_old' => $fc->doubleField('Старая розничная цена'),
				'price_wholesale_old' => $fc->doubleField('Старая оптовая цена'),
				'unit' => $fc->dropDownList_typeString('Единица измерения', self::getOptionsUnit()),
				'country_of_origin' => $fc->stringField('Страна производства', 50),
				'vendor' => $fc->stringField('Бренд производителя', 50),
				'flag_is_available' => $fc->singleCheckBox('В наличии'),
				'flag_is_delivery' => $fc->singleCheckBox('Есть доставка'),
//				'id_producer_goods' => $fc->checkBox('Товар от производителя', null, null, ['class' => 'js-id-producer-country-toggler']),
//				'id_producer_country' => $fc->autocomplete('Страна производитель', 'sts-producer-country', 'name'),
					//'discount_values' => $fc->doubleField('Размер скидки в процентах (если есть)')
			];

			$fields['name']['label'] = 'Название товара, фасовка';
			$fields['description']['label'] = 'Описание товара';
		}

		if ($this->model()->exists()) {
			$fields['id'] = $fc->hiddenField();
		}

		return $fields;
	}

	public static function getOptionsUnit() {
		return (new \App\Model\PriceUnit())->getListForDropDown();
	}

	// -------------------------------------------------------------------------

	public function render($heading, \App\Model\Firm $firm) {
		if (isset($this->params['step']) && (int)$this->params['step'] === 2) {
			return $this->renderStep2($heading, $firm);
		} else {
			return $this->renderStep1();
		}
	}

	private function renderStep1() {
		return $this->view()
						->set('fields', $this->renderFields())
						->setTemplate('form_add_step1', 'price/forms')
						->render();
	}

	private function renderStep2($heading, \App\Model\Firm $firm) {
		$ff = new \App\Model\Image();
		if ($this->model()->exists()) {
			$images = $ff->reader()
					->setWhere(['AND', '`id_firm` = :id_firm', 'id_price = :id_price', 'source = :source'], [':id_firm' => $firm->id(), ':source' => 'client', ':id_price' => $this->model()->id()])
					->setOrderBy('timestamp_inserting DESC')
					->objects();
			$pcp = new \App\Model\PriceCatalogPrice();
			$pcp->reader()->setWhere(['AND', 'id_price = :id_price'], [':id_price' => $this->model()->id()])->objectByConds();
			$this->setVal('id_catalog', $pcp->val('id_catalog'));
			$catalog = new \App\Model\PriceCatalog($pcp->val('id_catalog'));
		} else {
			$id_catalog = app()->request()->processGetParams(['id_catalog' => ['type' => 'int']])['id_catalog'];
			$catalog = new \App\Model\PriceCatalog($id_catalog);
			$this->setVal('id_catalog', $catalog->id());
			$this->setVal('id_group', $catalog->id_group());
			$this->setVal('id_subgroup', $catalog->id_subgroup());
			$images = $ff->reader()
					->setWhere(['AND', '`id_firm` = :id_firm', 'source = :source'], [':id_firm' => $firm->id(), ':source' => 'temp'])
					->setOrderBy('timestamp_inserting DESC')
					->objects();
		}

		$type = (int)$catalog->val('id_group') === 44 ? 'service' : ((int)$catalog->val('id_group') === 22 ? 'equipment' : 'goods');


		return $this->view()
						->set('type', $type) //todo
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('price_images', $images)
						->set('id_firm', $firm->id())
						->set('id_service', $firm->id_service())
						->set('model', $this->model())
						->setTemplate('form_add_step2', 'price/forms')
						->render();
	}

}
