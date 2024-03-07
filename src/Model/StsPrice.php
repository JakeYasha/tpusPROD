<?php

namespace App\Model;

use Foolz\SphinxQL\SphinxQL;

class StsPrice extends \Sky4\Model\Composite {

	use Component\IdFirmTrait,
	 Component\SourceTrait;

	private $table = 'sts_price';
	private $sts_price_firm = null;

	public function idFieldsNames() {
		return ['id'];
	}

	public function fields() {
		return [
			'id' => [
				'col' => [
					'default_val' => '',
					'flags' => 'auto_increment not_null primary_key unsigned',
					'name' => 'id',
					'type' => 'int_8',
				],
				'elem' => 'text_field',
				'label' => 'id'
			],
			'id_service' => [
				'col' => [
					'flags' => 'not_null key unsigned',
					'type' => 'int_2'
				],
				'elem' => 'hidden_field',
				'label' => 'ID службы',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_price' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_price',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_price'
			],
			'id_city' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_city',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_city'
			],
			'id_subgroup' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_subgroup',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_subgroup'
			],
			'id_producer_goods' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'id_producer_goods',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'id_producer_goods'
			],
			'id_producer_country' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'id_producer_country',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_producer_country'
			],
			'datetime' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'datetime',
					'type' => 'date_time',
				],
				'elem' => 'text_field',
				'label' => 'datetime'
			],
			'name' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'name',
					'type' => 'string(512)',
				],
				'elem' => 'text_field',
				'label' => 'name'
			],
			'unit' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'unit',
					'type' => 'string(32)',
				],
				'elem' => 'text_field',
				'label' => 'unit'
			],
			'pack' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'pack',
					'type' => 'string(32)',
				],
				'elem' => 'text_field',
				'label' => 'pack'
			],
			'info' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'info',
					'type' => 'text_2',
				],
				'elem' => 'text_field',
				'label' => 'info'
			],
			'id_group' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_group',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_group'
			],
			'blocked' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'blocked',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'blocked'
			],
			'indexed' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'indexed',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'indexed'
			],
			'manufacture' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'manufacture',
					'type' => 'string(64)',
				],
				'elem' => 'text_field',
				'label' => 'manufacture'
			],
			'exist_image' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'exist_image',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'exist_image'
			],
			'extended' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'extended',
					'type' => 'string(4)',
				],
				'elem' => 'text_field',
				'label' => 'extended'
			],
			'id_hist_image' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'id_hist_image',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_hist_image'
			],
			'assort' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'assort',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'assort'
			],
			'discount_yes' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'discount_yes',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'discount_yes'
			],
			'discount_values' => [
				'col' => [
					'default_val' => '0.00',
					'flags' => 'not_null',
					'name' => 'discount_values',
					'type' => 'decimal(11,2)',
				],
				'elem' => 'text_field',
				'label' => 'discount_values'
			],
			'id_hist_image_kupon' => [
				'col' => [
					'default_val' => '0',
					'flags' => '',
					'name' => 'id_hist_image_kupon',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_hist_image_kupon'
			],
			'id_description' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_description',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_description'
			],
			'extended_description' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'extended_description',
					'type' => 'string(4)',
				],
				'elem' => 'text_field',
				'label' => 'extended_description'
			],
			'description_comment' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'description_comment',
					'type' => 'string(80)',
				],
				'elem' => 'text_field',
				'label' => 'description_comment'
			],
			'deadline' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'deadline',
					'type' => 'date_time',
				],
				'elem' => 'text_field',
				'label' => 'deadline'
			],
			'sale1' => [
				'col' => [
					'default_val' => '0',
					'flags' => '',
					'name' => 'sale1',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'sale1'
			],
			'sale2' => [
				'col' => [
					'default_val' => '0',
					'flags' => '',
					'name' => 'sale2',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'sale2'
			],
			'publish' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'publish',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'publish'
			],
			'internet' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'internet',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'internet'
			],
			'dispetcher' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'dispetcher',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'dispetcher'
			],
			'have_price' => [
				'col' => [
					'default_val' => '0',
					'flags' => '',
					'name' => 'have_price',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'have_price'
			],
			'flag_is_for_sale' => [
				'col' => [
					'default_val' => '1',
					'flags' => '',
					'name' => 'flag_is_for_sale',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'flag_is_for_sale'
			],
			'deleted' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'deleted',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'deleted'
			],
			'images' => [
				'col' => [
					'default_val' => '',
					'type' => 'string(2000)'
				],
				'elem' => 'text_field',
				'label' => 'Изображения',
			]
		];
	}

	public function photoLink($id_firm, $id_service, $filename) {
		//@todo
		return 'http://www.tovaryplus.ru/img/ratiss/upl/' . $id_firm . '_' . $id_service . '/' . $filename;
		//return '/img/ratiss/upl/42632_10/f27490c6cc394f2d21af865f63126527.jpg';
	}

	public function name() {
		return $this->val('name');
	}

	public static function priceQueryChunk() {
		return 'SELECT
			`sts_price`.*,
			`firm`.`company_name` as `company_name`,
			`firm`.`company_phone` as `company_phone`,
			`firm`.`company_email` as `company_email`,
			`firm`.`company_map_address` as `company_address`,
			`sts_producer_country`.`name` as producerName,
			(SELECT CONCAT("/image/",`image`.`file_subdir_name`,"/",`image`.`file_name`,".",`image`.`file_extension`,"~",`image`.`id`) FROM `image` WHERE `image`.`id_price`=`sts_price`.`id_price` LIMIT 1) as `image`
			FROM `sts_price`
			LEFT JOIN `firm` ON `firm`.`id`=`sts_price`.`id_firm`
			LEFT JOIN `sts_producer_country` ON `sts_producer_country`.`id_producer_country`=`sts_price`.`id_producer_country` AND `firm`.`id_service`=`sts_price`.`id_service`';
	}

	public static function getCostRow($id_price) {
		$result = app()->db()->query()
				->setSelect([
					'`sts_cost`.`cost` as cost',
					'`sts_cost`.`id_sale` as `id_sale`',
					'`sts_payment`.`name` as paymentType',
					'`sts_sale`.`name` as saleType',
					'`sts_currency`.`name` as currency'
				])
				->setFrom('sts_cost')
				->setLeftJoin('sts_payment', '`sts_payment`.`id_payment`=`sts_cost`.`id_payment`')
				->setLeftJoin('sts_sale', '`sts_sale`.`id_sale`=`sts_cost`.`id_sale`')
				->setLeftJoin('sts_currency', '`sts_currency`.`id_currency`=`sts_cost`.`id_currency`')
				->setWhere([
					'AND',
					'id_price = :id_price',
					'cost > :cost',
						], [
					':cost' => 0.00,
					':id_price' => $id_price,
				])
				->setGroupBy('id_sale')
				->setOrderBy('saleType ASC')
				->select();

		return $result;
	}

	public static function combine($item, $firm, $price = [], $default_catalog_image = null) {
		$price_retail = null;
		$price_wholesale = null;
		$currency = null;

		foreach ($price as $row) {
			if ((int) $row['id_sale'] === 1) {
				$price_wholesale = $row['cost'];
			} elseif ((int) $row['id_sale'] === 2 || (int) $row['id_sale'] === 0) {
				$price_retail = $row['cost'];
			}
			$currency = $row['currency'];
		}

		$old_price = ($item['discount_values'] !== '0.00' && $price_retail !== null) ? str()->addSpaces(intval($price_retail * 1 / ((100 - $item['discount_values']) / 100))) : null;

		if ($price_retail !== null) {
			$price_retail = str()->addSpaces(intval($price_retail));
		}
		if ($price_wholesale !== null) {
			$price_wholesale = str()->addSpaces(intval($price_wholesale));
		}

		if (isset($item['image']) && $item['image']) {
			$chunks = explode('~', $item['image']);
			$item['image_id'] = $chunks[1];
			$item['image'] = $chunks[0];
			$item['image_thumb'] = str()->replace($item['image'], '.', '-160x160.');
			if (!file_exists(APP_DIR_PATH . '/public' . $item['image_thumb'])) {
				$item['image_thumb'] = null;
			}
		}

		if (!$item['image'] && $default_catalog_image instanceof File && $default_catalog_image->exists()) {
			$item['image'] = $default_catalog_image->link('-260x260');
			$item['image_thumb'] = $default_catalog_image->link('-260x260');
		}

		return [
			'id' => $item['id'],
			'id_price' => $item['id_price'],
			'id_group' => $item['id_group'],
			'id_subgroup' => $item['id_subgroup'],
			'price' => $price_retail !== null ? $price_retail : $price_wholesale,
			'price_retail' => $price_retail,
			'price_wholesale' => $price_wholesale,
			'currency' => $currency === 'Р' ? '<i class="r">b</i>' : $currency,
			'firm' => $firm,
			'name' => $item['name'],
			'link' => '/price/show/' . $item['id_price'] . '/' . $item['id_service'] . '/',
			'unit' => ($price_retail !== null || $price_wholesale !== null) ? str()->toLower($item['unit']) : null,
			'old_price' => $old_price,
			'pack' => str()->length(trim($item['pack'])) > 1 ? trim($item['pack']) : '',
			'image' => $item['image'] ? $item['image'] : null,
			'image_thumb' => (isset($item['image_thumb']) && isset($item['image_thumb'])) ? $item['image_thumb'] : null,
			'image_id' => $item['image_id'] ?? null,
			'production' => $item['id_producer_country'] ? str()->firstCharToUpper(str()->toLower($item['producerName'])) : null,
			'datetime' => $item['datetime'],
			'flag_is_for_sale' => $item['flag_is_for_sale'],
			'info' => $item['id_service'] == 10 ? str()->replace(nl2br($item['info'], true), "\\", '') : str()->sentance(str()->firstCharToUpper(str()->toLower(trim(nl2br(preg_replace_callback('~href="([^"]+)"~u', function($matches) use($item) {
																return 'target="_blank" rel="nofollow" href="' . app()->away(trim($matches[1]), $item['id_firm']) . '"';
															}, $item['info']), true)))), false)
		];
	}

	public function prepare($path = []) {
		$query = self::priceQueryChunk() . ' WHERE `sts_price`.`id` = ' . $this->id();

		$items = app()->db()->query()
				->setText($query)
				->fetch();

		$item = $items[0];
		$price = self::getCostRow($item['id_price']);

		$firm = new Firm();
		$firm->setVals($item);

		$rev_path = array_reverse($path);
		$default_image = null;
		foreach ($rev_path as $cat) {
			if ($cat->val('image')) {
				$default_image = $cat->imageComponent()->get();
				break;
			}
		}

		return self::combine($item, $firm, $price, $default_image);
	}

	public function table() {
		return $this->table;
	}

	public function link() {
		return '/price/show/' . $this->val('id_price') . '/' . $this->val('id_service') . '/';
	}

	public function linkItem() {
		return '/price/show/' . $this->val('id_price') . '/' . $this->val('id_service') . '/';
	}

	public function setTable($table) {
		$this->table = $table;
	}

	public function getByIdService($id_price, $id_service) {
		return $this->reader()
						->setWhere(['AND', '`id_price` = :id_price', '`id_service` = :id_service'], [':id_price' => $id_price, ':id_service' => $id_service])
						->objectByConds();
	}

	public function insert($vals = null, $parent_object = null) {
		$result = parent::insert($vals, $parent_object);
		$this->updateRtIndex();

		return $result;
	}

	public function update($vals = null) {
		$result = parent::update($vals);
		$this->updateRtIndex();

		return $result;
	}

	public function updateRtIndex($sphinx = null) {
		if ($sphinx === null) {
			$sphinx = SphinxQL::create(app()->getSphinxConnection());
		}

		$firm = new \App\Model\Firm();
		$firm->getByIdFirmAndIdService($this->id_firm(), $this->id_service());
		$cost = new \App\Model\StsCost();
		$min_cost = $cost->reader()
				->setWhere(['AND', 'id_price = :id_price', 'id_firm = :id_firm', 'id_service = :id_service', 'cost > :cost'], [':id_price' => $this->val('id_price'), ':id_firm' => $this->id_firm(), ':id_service' => $this->id_service(), ':cost' => 0])
				->min('cost');
		$have_info = str()->length($this->val('info')) > 10;
		$have_price = $min_cost > 0;

		$have_inf_img_price = 0;
		if ($have_price) {
			$have_inf_img_price += 6;
		}
		if ($have_info) {
			$have_inf_img_price += 3;
		}

		if ($this->val('exist_image')) {
			$have_inf_img_price += 1;
		}

		$timestamp = new \Sky4\Helper\DateTime($this->val('datetime'));

		$name = trim(str()->replace(str()->replace(str()->replace($this->val('name'), ')', ''), '(', ''), '-', ' '));
		$words = explode(' ', $name);
		$row = [
			'id' => $this->id(),
			'name' => $name,
			'wname' => $words[0],
			'w2name' => $words[0] . (isset($words[1]) ? ' ' . $words[1] : ''),
			'info' => (string) $this->val('info'),
			'id_firm' => $firm->id(),
			'id_producer_goods' => $this->val('id_producer_goods'),
			'id_region_city' => $firm->val('id_region_city'),
			'id_subgroup' => $this->val('id_subgroup'),
			'id_description' => $this->val('id_description'),
			'discount_yes' => $this->val('discount_yes'),
			'exist_image' => $this->val('exist_image'),
			'sale1' => $this->val('sale1'),
			'sale2' => $this->val('sale2'),
			'length' => ((str()->length($name) - str()->length(str()->replace($name, ' ', '')))) + 1,
			'have_price' => $have_price ? 1 : 0,
			'have_info' => $have_info ? 1 : 0,
			'have_inf_img_prc' => $have_inf_img_price,
			'blocked' => $this->val('blocked'),
			'timestamp' => $timestamp->timestamp(),
			'cost' => $min_cost,
			'firm_rating' => $firm->val('rating'),
            'firm_priority' => $firm->val('priority'),
			'datetime' => $timestamp->timestamp(),
			'sortname' => str()->toLower($this->val('name')),
			'flag_is_for_sale' => $this->val('flag_is_for_sale'),
				//'assort' => $this->val('assort'),
		];

		$sphinx->replace()
				->into(SPHINX_PRICE_INDEX)
				->set($row)
				->execute();

		return $this;
	}

	/**
	 * 
	 * @return \App\Model\Firm
	 */
	public function getFirm() {
		if ($this->sts_price_firm === null) {
			$firm = new Firm();
			if ($this->val('source') === 'ratiss') {
				$firm->reader()
						->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service'], [':id_firm' => $this->val('id_firm'), ':id_service' => $this->val('id_service')])
						->objectByConds();
			} else {
				$firm->reader()->object($this->val('id_firm'));
			}

			$this->sts_price_firm = $firm;
		}

		return $this->sts_price_firm;
	}

	public function forSale() {
		$firm = $this->getFirm();
		if ($firm->canSell() && $this->val('name')) {
			return true;
		}

		return false;
	}

}

/*
 *  [
					'id' => $item['id'],
					'id_subgroup' => $item['id_subgroup'],
					'currency' => isset($price[0]) ? ($price[0]['currency'] == 'Р' ? '<i class="r">b</i>' : 'Р') : null,
					'image' => $item['exist_image'] ? $item['image'] : null,
					'info' => str()->sentance(str()->toLower($item['info']), false),
					'firm' => $firm,
					'link' => '/price/show/' . $item['id_price'] . '/' . $item['id_service'] . '/',
					'name' => $item['name'],
					'number' => $i,
					'old_price' => ($item['discount_values'] !== '0.00' && isset($price[0])) ? str()->addSpaces(intval($price[0]['cost'] * (1 + $item['discount_values']))) : null,
					'pack' => str()->length($item['pack']) > 2 ? $item['pack'] : false,
					'price' => isset($price[0]) ? str()->addSpaces(intval($price[0]['cost'])) : null,
					'production' => $item['id_producer_country'] ? str()->firstCharToUpper(str()->toLower($item['producerName'])) : null,
					'unit' => str()->toLower($item['unit']),
				]
 */