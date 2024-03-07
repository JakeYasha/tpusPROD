<?php

namespace App\Model;

class Price extends \Sky4\Model\Composite {

	use Component\ActiveTrait,
	 Component\SourceTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait;

	private $price_firm = null;

	public function idFieldsNames() {
		return ['id'];
	}

	public function objectByYmlOffer(YmlOffer $offer) {
		$this->reader()
				->setWhere(['AND', 'id_external = :id_yml', 'id_firm = :id_firm', 'source = :source'], [':id_yml' => $offer->val('id_yml'), ':id_firm' => $offer->id_firm(), ':source' => 'yml'])
				->objectByConds();
		return $this;
	}

	public function refreshByYmlOffer(YmlOffer $offer) {
		$this->reader()
				->setWhere(['AND', 'id_external = :id_yml', 'id_firm = :id_firm', 'source = :source'], [':id_yml' => $offer->val('id_yml'), ':id_firm' => $offer->id_firm(), ':source' => 'yml'])
				->objectByConds();

		$insert_mode = 'insert';
		if ($this->exists()) {
			$insert_mode = 'update';
		}


		$images = $image = '';
		$_images = explode(',', $offer->val('images'));
		if (isset($_images[0])) {
			$image = $_images[0];
			unset($_images[0]);
			if ($_images) {
				$images = implode(',', $_images);
			}
		}

		$params = [];
		$param = new YmlParam();
		$_params = $param->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_yml_offer = :id_yml_offer'], [':id_firm' => $offer->id_firm(), ':id_yml_offer' => $offer->val('id_yml')])
				->setOrderBy('id ASC')
				->objects();

		foreach ($_params as $param) {
			$params[] = $param->name().'~'.$param->val('val');
		}

		$yml_cat = new YmlCategory($offer->val('id_yml_category'));
		$id_group = 0;
		$id_subgroup = 0;
		if ($yml_cat->exists() && (int)$yml_cat->val('id_catalog') > 0) {
			$pc = new PriceCatalog($yml_cat->val('id_catalog'));
			$id_group = $pc->val('id_group');
			$id_subgroup = $pc->val('id_subgroup');
		}

		//make images and so on
		if ($insert_mode === 'insert') {
			$vals = [
				'flag_is_active' => $offer->val('status') === 'deleted' ? 0 : 1,
				'flag_is_referral' => (int)$offer->val('flag_is_referral'),
				'source' => 'yml',
				'id_firm' => $offer->val('id_firm'),
				'timestamp_inserting' => \Sky4\Helper\DateTime::now()->format(),
				'timestamp_last_updating' => $offer->val('timestamp'),
				//
				'id_subgroup' => $id_subgroup,
				'id_group' => $id_group,
				'id_external' => $offer->val('id_yml'),
				//
				'country_of_origin' => $offer->val('country_of_origin'),
				'currency' => $offer->val('currency'),
				'description' => $offer->val('description'),
				'name' => $offer->name(),
				'name_external' => $offer->name(),
				'price' => $offer->val('price'),
				'price_old' => $offer->val('old_price'),
				'price_wholesale' => 0,
				'price_wholesale_old' => 0,
				'params' => implode(PHP_EOL, $params),
				'url' => $offer->val('url'),
				'vendor' => $offer->val('vendor'),
				'unit' => 'шт.',
				//
				'flag_is_available' => $offer->val('flag_is_available'),
				'flag_is_delivery' => $offer->val('flag_is_delivery'),
				'flag_is_image_exists' => (int)($image || $images),
				'flag_is_retail' => (int)$offer->val('price') > 0,
				'flag_is_wholesale' => 0
			];
		} else {
			$vals = [
				'flag_is_active' => $offer->val('status') === 'deleted' ? 0 : 1,
				'flag_is_referral' => (int)$offer->val('flag_is_referral'),
				'id_subgroup' => $id_subgroup,
				'id_group' => $id_group,
				'id_external' => $offer->val('id_yml'),
				'flag_is_active' => 1,
				'source' => 'yml',
				'id_firm' => $offer->val('id_firm'),
				//
				'timestamp_last_updating' => $offer->val('timestamp'),
				'country_of_origin' => $offer->val('country_of_origin'),
				'currency' => $offer->val('currency'),
				'description' => $offer->val('description'),
				'name' => $offer->name(),
				'name_external' => $offer->name(),
				'price' => $offer->val('price'),
				'price_old' => $offer->val('old_price'),
				'params' => implode(PHP_EOL, $params),
				'url' => $offer->val('url'),
				'vendor' => $offer->val('vendor'),
				//
				'flag_is_available' => $offer->val('flag_is_available'),
				'flag_is_delivery' => $offer->val('flag_is_delivery'),
				'flag_is_image_exists' => (int)($image || $images),
				'flag_is_retail' => (int)$offer->val('price') > 0
			];
		}

		if ($insert_mode === 'insert') {
			$this->insert($vals);
		} else {
			$this->update($vals);
		}

		return $this;
	}

	public function fields() {
		$c = $this->fieldPropCreator();

		return [
			'id' => $c->intField('ID', 8, ['rules' => ['int']], ['flags' => 'auto_increment not_null primary_key unsigned']),
			'id_subgroup' => $c->intField('Подгруппа (только для рассчета каталога)', 2, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			'id_group' => $c->intField('Группа (только для расчета каталога)', 2, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			'id_external' => $c->intField('ID id_price для РАТИСС или id_yml для YML', 8, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			//
			'country_of_origin' => $c->stringField('Страна производства', 36),
			'currency' => $c->stringField('Валюта', 3),
			'description' => $c->textArea('Описание'),
			'name' => $c->stringField('Название', 500),
			'name_external' => $c->stringField('Название из источника', 500),
			'price' => $c->priceField('Цена'),
			'price_old' => $c->priceField('Старая цена'),
			'price_wholesale' => $c->priceField('Оптовая цена'),
			'price_wholesale_old' => $c->priceField('Старая оптовая цена'),
			'params' => $c->textArea('Параметры предложения (ключ-значение)'),
			'url' => $c->stringField('Url товара (для YML товаров)', 1024),
			'vendor' => $c->stringField('Производитель', 100),
			'unit' => $c->stringField('Единича измерения', '10'),
			//
			'flag_is_available' => $c->singleCheckBox('В наличии/Под заказ'),
			'flag_is_delivery' => $c->singleCheckBox('Доставка и самовывоз (базовый регион)/Только самовывоз'),
			'flag_is_image_exists' => $c->singleCheckBox('Есть ли изображение у товара'),
			'flag_is_retail' => $c->singleCheckBox('Есть цена в розницу'),
			'flag_is_referral' => $c->singleCheckBox('Использовать рефферальную ссылку'),
			'flag_is_wholesale' => $c->singleCheckBox('Есть цена оптом'),
			//
			'legacy_id_service' => $c->intField('тлен', 2, ['rules' => ['int']], ['flags' => 'not_null key']),
			'legacy_id_price' => $c->intField('тлен', 8, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			'legacy_id_firm' => $c->intField('тлен', 4, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			'legacy_id_city' => $c->intField('тлен', 4, ['rules' => ['int']], ['flags' => 'not_null unsigned']),
		];
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

	public function updateRtIndex($sphinx = null, $is_draft_index = null) {
		if ($sphinx === null) {
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		if ((int)$this->val('flag_is_active') === 1) {

			$firm = new \App\Model\Firm();
			$firm->reader()->object($this->id_firm());

			$costs = [];
			if ((double)$this->val('price') > 0) {
				$costs[] = (double)$this->val('price');
			}
			if ((double)$this->val('price_old') > 0) {
				$costs[] = (double)$this->val('price_old');
			}
			if ((double)$this->val('price_wholesale') > 0) {
				$costs[] = (double)$this->val('price_wholesale');
			}
			if ((double)$this->val('price_wholesale_old') > 0) {
				$costs[] = (double)$this->val('price_wholesale_old');
			}

			if ($costs) {
				$cost = (double)min($costs);
			} else {
				$cost = 0.00;
			}

			$have_info = str()->length($this->val('description')) > 10;
			$have_price = $cost > 0.00;

			$have_inf_img_price = 0;
			if ($have_price) {
				$have_inf_img_price += 6;
			}
			if ($have_info) {
				$have_inf_img_price += 3;
			}

			if ($this->val('flag_is_image_exists')) {
				$have_inf_img_price += 1;
			}

			$timestamp = new \Sky4\Helper\DateTime($this->val('timestamp_last_updating'));

			$name = trim(str()->replace(str()->replace(str()->replace($this->val('name'), ')', ''), '(', ''), '-', ' '));
			$words = explode(' ', $name);
			$row = [
				'id' => $this->id(),
				'name' => $name,
				'wname' => $words[0],
				'w2name' => $words[0].(isset($words[1]) ? ' '.$words[1] : ''),
				'info' => (string)$this->val('description'),
				'id_firm' => $firm->id(),
				'id_subgroup' => $this->val('id_subgroup'),
				'discount_yes' => ((double)$this->val('price') < (double)$this->val('price_old') || (double)$this->val('price_wholesale') < (double)$this->val('price_wholesale_old')) ? 1 : 0,
				'exist_image' => (int)$this->val('flag_is_image_exists'),
				'sale1' => (int)$this->val('flag_is_retail') === 1,
				'sale2' => (int)$this->val('flag_is_wholesale') === 1,
				'length' => ((str()->length($name) - str()->length(str()->replace($name, ' ', '')))) + 1,
				'have_price' => $have_price ? 1 : 0,
				'have_info' => $have_info ? 1 : 0,
				'have_inf_img_prc' => $have_inf_img_price,
				'blocked' => (int)$this->val('flag_is_active') === 0,
				'timestamp' => $timestamp->timestamp(),
				'cost' => $cost,
				'firm_rating' => $firm->val('rating'),
				'firm_priority' => $firm->val('priority'),
				'datetime' => $timestamp->timestamp(),
				'sortname' => str()->toLower($this->val('name')),
				'unit' => $this->val('unit'),
				'country_of_origin' => $this->val('country_of_origin'),
				'yml' => (int)$this->sourceIsYml(),
                'vendor' => $this->val('vendor')
			];
			
			$sphinx->replace()
					->into($is_draft_index ? SPHINX_PRICE_DRAFT_INDEX : SPHINX_PRICE_INDEX)
					->set($row)
					->execute();
		} else {
			$sphinx->delete()
					->from($is_draft_index ? SPHINX_PRICE_DRAFT_INDEX : SPHINX_PRICE_INDEX)
					->where('id', '=', (int)$this->id())
					->execute();
		}

		return $this;
	}

	public function deleteRtIndex($sphinx = null) {
		if ($sphinx === null) {
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		$sphinx->delete()
				->from(SPHINX_PRICE_INDEX)
				->where('id', '=', intval($this->id()))
				->execute();

		return $this;
	}

	public function deleteRtIndexByIdFirm($id_firm) {
		app()->resetSphinxConnection();
		$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		$sphinx->delete()
				->from(SPHINX_PRICE_INDEX)
				->where('id_firm', '=', intval($id_firm))
				->execute();

		return $this;
	}

	public function delete($sphinx = null) {
		$this->deleteRtIndex($sphinx);
		return parent::delete();
	}

	public static function priceQueryChunk() {
		return 'SELECT
			`price`.*,
			`firm`.`company_name` as `company_name`,
			`firm`.`company_phone` as `company_phone`,
			`firm`.`company_email` as `company_email`,
			`firm`.`company_map_address` as `company_address`,
			`price`.`vendor` as producerName,
			(SELECT CONCAT("/image/",`image`.`file_subdir_name`,"/",`image`.`file_name`,".",`image`.`file_extension`,"~",`image`.`id`) FROM `image` WHERE `image`.`id_price`=`price`.`id` LIMIT 1) as `image`,
			(SELECT CONCAT("/yml_image/",`yml_image`.`file_subdir_name`,"/",`yml_image`.`file_name`,".",`yml_image`.`file_extension`,"~",`yml_image`.`id`) FROM `yml_image` WHERE `yml_image`.`id_firm` = `price`.`id_firm` AND `yml_image`.`id_yml` = `price`.`id_external` LIMIT 1) as `image_yml`
			FROM `price`
			LEFT JOIN `firm` ON `firm`.`id`=`price`.`id_firm`';
	}

	public static function catalogQueryChunk($ids) {
		return 'SELECT
			`price`.*,
			`firm`.`company_name` as `company_name`,
			`firm`.`company_phone` as `company_phone`,
			`firm`.`company_email` as `company_email`,
			`firm`.`company_map_address` as `company_address`,
			`sts_producer_country`.`name` as producerName,
			`photo`.`filename` as `photo` ,
			(SELECT CONCAT("/image/",`image`.`file_subdir_name`,"/",`image`.`file_name`,".",`image`.`file_extension`,"~",`image`.`id`) FROM `image` WHERE `image`.`id_price`=`price`.`id_price` LIMIT 1) as `image`,
			(SELECT CONCAT("/yml_image/",`yml_image`.`file_subdir_name`,"/",`yml_image`.`file_name`,".",`yml_image`.`file_extension`,"~",`yml_image`.`id`) FROM `yml_image` WHERE `yml_image`.`id_firm` = `price`.`id_firm` AND `yml_image`.`id_yml` = `price`.`id_external` LIMIT 1) as `image_yml`
			FROM `price`
			LEFT JOIN `firm` ON `firm`.`id`=`price`.`id_firm`,
			LEFT JOIN `photo` ON `photo`.`id_price`=`sts_price`.`legacy_id_price`
			WHERE `price`.`id` IN ('.$ids.') 
			ORDER BY FIELD(`price`.`id`,'.$ids.')';
	}

	public static function combine($item, $firm, $price = [], $default_catalog_image = null, $get_images = TRUE) {
		$price_retail = (double)$item['price'];
		$price_wholesale = (double)$item['price_wholesale'];
		$price_retail_old = (double)$item['price_old'];
		$price_wholesale_old = (double)$item['price_wholesale_old'];
		$currency = $item['currency'];

		$price_retail = $price_retail > 0 ? self::renderPrice($price_retail) : 0;
		$price_wholesale = $price_wholesale > 0 ? self::renderPrice($price_wholesale) : 0;
		$price_retail_old = $price_retail_old > 0 ? self::renderPrice($price_retail_old) : 0;
		$price_wholesale_old = $price_wholesale_old > 0 ? self::renderPrice($price_wholesale_old) : 0;

		if ($get_images && isset($item['image']) && $item['image']) {
			$chunks = explode('~', $item['image']);
			$item['image_id'] = $chunks[1];
			$item['image'] = $chunks[0];
			$item['image_thumb'] = str()->replace($item['image'], '.', '-160x160.');
			if (!file_exists(APP_DIR_PATH.'/public'.$item['image_thumb'])) {
				$item['image_thumb'] = $item['image'];
			}
		}

		if ($get_images && isset($item['image_yml']) && $item['image_yml']) {
			$chunks = explode('~', $item['image_yml']);
			$item['image_id'] = $chunks[1];
			$item['image'] = $chunks[0];
			$item['image_thumb'] = str()->replace($item['image'], '.', '-160x160.');
			if (!file_exists(APP_DIR_PATH.'/public'.$item['image_thumb'])) {
				$item['image_thumb'] = $item['image'];
			}
		}
        
		$images = [];
		$images_base_path = null;
		if ($get_images && isset($item['image_id']) && $item['image_id']) {
			if ($item['source'] !== 'yml') {
                //ВРЕМЕННО ОТКЛЮЧЕНО
				//$images = (new Image())->reader()->setWhere(['AND', 'id_price = :id_price', 'id != :image_id'], [':id_price' => $item['id'], 'image_id' => $item['image_id']])->objects();
				$images_base_path = '/image';
			} else {
                // ВРЕМЕННО ОТКЛЮЧЕНО!!!
				//$images = (new YmlImage())->reader()->setWhere(['AND', 'id_yml = :id_external', 'id != :image_id'], [':id_external' => $item['id_external'], 'image_id' => $item['image_id']])->objects();
				$images_base_path = '/yml_image';
			}
		}

		if ($get_images && !$item['image'] && $default_catalog_image instanceof File && $default_catalog_image->exists()) {
			$item['image'] = $default_catalog_image->link('-260x260');
			$item['image_thumb'] = $default_catalog_image->link('-260x260');
		}
        
        if (/*APP_IS_DEV_MODE*/true) {
            if (!$firm->isForCurrentCity()) {
                $firm_branches = $firm->getCityFirmBranches();
                if (count($firm_branches) > 0) {
                    $firm_branch = current($firm_branches);
                    $vals = $firm_branch->getVals();
                    unset($vals['id']);
                    $firm->setVals($vals);
                    $firm->branch_id = $firm_branch->id();
                    $firm->flag_is_price_attached = $firm_branch->val('flag_is_price_attached');
                }            
            }
        }
        
        $result = [
			'id' => $item['id'],
			'id_price' => $item['legacy_id_price'],
			'id_firm' => $item['id_firm'],
			'id_group' => $item['id_group'],
			'id_subgroup' => $item['id_subgroup'],
			'price' => $price_retail > 0 ? $price_retail : $price_wholesale,
			'price_retail' => $price_retail,
			'price_wholesale' => $price_wholesale,
			'price_retail_old' => $price_retail_old,
			'price_wholesale_old' => $price_wholesale_old,
			'currency' => ($currency === 'RUR' || $currency === 'RUB') ? '<i class="r">b</i>' : $currency,
			'firm' => $firm,
			'name' => $item['name'],
			'link' => self::staticLink($item),
			'link_tp' => self::staticLink($item, 'tp'),
			'unit' => ($price_retail > 0 || $price_wholesale > 0) ? str()->toLower($item['unit']) : null,
			'old_price' => $price_retail_old > 0 ? $price_retail_old : ($price_wholesale_old > 0 ? $price_wholesale_old : null),
			'pack' => '',
            'image_id' => $item['image_id'] ?? null,
			'production' => $item['country_of_origin'],
			'vendor' => $item['producerName'],
			'datetime' => $item['timestamp_last_updating'],
			'flag_is_for_sale' => 0,
			'flag_is_referral' => (int)$item['flag_is_referral'],
			'info' => self::getDescription($item['description']),
			'description' => self::getDescription($item['description']),
			'description_short' => self::getShortDescription($item['description'], self::staticLink($item, 'tp')),
			'description_short_away' => self::getShortDescription($item['description'], self::staticLink($item), $item['source'] === 'yml'),
			'country_of_origin' => $item['country_of_origin'],
			'is_yml' => $item['source'] === 'yml'
		];
                
        if ($get_images) {
			$result['image'] = $item['image'] ? $item['image'] : null;
			$result['image_thumb'] = (isset($item['image_thumb']) && isset($item['image_thumb'])) ? $item['image_thumb'] : null;
			$result['images'] = $images;
			$result['images_base_path'] = $images_base_path;
        }
        
        return $result;
	}

	/**
	 * 
	 * @return \App\Model\Firm
	 */
	public function getFirm() {
		if ($this->price_firm === null) {
			$this->price_firm = new Firm($this->val('id_firm'));
		}

		return $this->price_firm;
	}

	public function forSale() {
		$firm = $this->getFirm();
		if ($firm->canSell() && $this->val('name')) {
			return true;
		}

		return false;
	}

	public function photoLink($id_firm, $id_service, $filename) {
		//@todo
		return 'http://www.tovaryplus.ru/img/ratiss/upl/'.$id_firm.'_'.$id_service.'/'.$filename;
		//return '/img/ratiss/upl/42632_10/f27490c6cc394f2d21af865f63126527.jpg';
	}

	public static function staticLink($item = [], $mode = 'default') {
		if ($mode === 'tp') {
			if ($item['legacy_id_price'] && $item['legacy_id_service']) {
				return '/price/show/'.$item['legacy_id_price'].'/'.$item['legacy_id_service'].'/';
			}
			return '/price/show/'.$item['id'].'/';
		}

		return /*(int)$item['flag_is_referral'] === 1 &&*/ $item['url'] ? ('/price/away/'.$item['id'].'/') : ((int)$item['legacy_id_service'] !== 0 ? ('/price/show/'.$item['legacy_id_price'].'/'.$item['legacy_id_service'].'/') : '/price/show/'.$item['id'].'/');
	}

	public function link() {
		return /*(int)$this->val('flag_is_referral') === 1 &&*/ $this->val('url') ? ('/price/away/'.$this->id().'/') : ((int)$this->val('legacy_id_service') !== 0 ? ('/price/show/'.$this->val('legacy_id_price').'/'.$this->val('legacy_id_service').'/') : '/price/show/'.$this->id().'/');
	}

	public function prepare($path = []) {
		$query = self::priceQueryChunk().' WHERE `price`.`id` = '.$this->id();
		$items = app()->db()->query()
				->setText($query)
				->fetch();

		$item = $items[0];

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

		return self::combine($item, $firm, [], $default_image);
	}

	public function val($field_name, $default_val = null) {
		if ($field_name === 'id_price') {
			return $this->val('legacy_id_price') ? $this->val('legacy_id_price') : $this->id();
		}
		return parent::val($field_name, $default_val);
	}

	public static function getShortDescription($text, $link, $nofollow = false) {
		$text = strip_tags($text);
		$text = str()->replace($text, "\\", '<br/>');
		if (str()->length($text) > 350) {
			$crop_before = 350;
			$text = str()->crop($text, $crop_before, '.', '...').' <a ' . ($nofollow ? 'rel="nofollow"' : '' ) . ' href="'.$link.'">подробнее</a>';
		}

		return $text;
	}

	public static function getDescription($text) {
		return str()->replace($text, "\\", '<br/>');
	}

	public static function renderPrice($price) {
		$price_arr = explode('.', (string)$price);
		if (count($price_arr) === 2) {
			$price = str()->addSpaces(trim($price_arr[0])).','.$price_arr[1];
		} else {
			$price = str()->addSpaces($price);
		}

		return $price;
	}

}
