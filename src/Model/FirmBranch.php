<?php

namespace App\Model;

class FirmBranch extends \Sky4\Model\Composite {

	private $about_text = null;

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\ActiveTrait,
	 Component\GeoDataTrait,
	 Component\CompanyDataTrait,
	 Component\TextTrait,
	 Component\TimestampActionTrait,
	 Component\SourceTrait;

	public function id() {
		return (int)parent::id();
	}

    public function firm() {
		return new Firm($this->val('firm_id'));
	}

    
	public function renderPhoneLinks() {
		$links = [];
		$phones = explode(', ', $this->val('company_phone'));
		$geo_data = $this->geoDataComponent()->getGeoData();

		$region_code = $geo_data['cityCode'];
		$country_code = $geo_data['countryCode'];
		foreach ($phones as $phone) {
			$prefix = str()->sub($phone, 0, 2);
			if ($prefix === '8 ' || $prefix === '8-') {
				$links[] = $this->getCellPhone($phone, true, $this);
			} else {
				$phone = str()->replace($phone, 'рыбинск', 'Рыбинск');
				$phone = str()->replace($phone, 'ростов', 'Ростов');
				$phone = str()->replace($phone, 'костром', 'Костром');
				$phone = str()->replace($phone, ' рф', ' РФ');
				$phone = '+'.($country_code == '0' ? 7 : $country_code).' ('.$region_code.') '.$phone;
				$links[] = [
					'href' => 'tel:+'.preg_replace('~[^0-9]~', '', $phone),
					'name' => trim($phone),
					'class' => 'tel brand-list__item--link',
                    'data-firm-id' => $this->val('firm_id')
				];
			}
		}

		$_links = [];
		foreach ($links as $link) {
			$_links[$link['href']] = $link;
		}

		return app()->chunk()->set('items', $_links)->render('common.links_list');
	}
    
    private static function getCellPhone($phone, $eight_mode = false, $firm = false) {
		$phone = preg_replace('~[^0-9]~u', '', $phone);
		if ($eight_mode) {
			$phone = '+7'.str()->sub($phone, 1);
		} else {
			$phone = '+'.$phone;
		}

		$cell_phone_code = str()->sub($phone, 0, 2);
		$cell_phone_operator = str()->sub($phone, 2, 3);
		$cell_phone_part_one = str()->sub($phone, 5, 3);
		$cell_phone_part_two = str()->sub($phone, 8, 2);
		$cell_phone_part_three = str()->sub($phone, 10, 2);

		if ($cell_phone_code) return [
				'href' => 'tel:'.$cell_phone_code.$cell_phone_operator.$cell_phone_part_one.$cell_phone_part_two.$cell_phone_part_three,
				'name' => $cell_phone_code.' ('.$cell_phone_operator.') '.$cell_phone_part_one.'-'.$cell_phone_part_two.'-'.$cell_phone_part_three,
				'class' => 'tel brand-list__item--link',
                'data-firm-id' => $firm->val('firm_id')
			];
	}

	public function renderWebLinks() {
		$links = [];

        foreach ($this->webSiteUrls() as $url) {
            $links[] = [
                'target' => '_blank',
                'href' => app()->away($url, $this->id()),
                'rel' => 'nofollow',
                'name' => trim($url),
                'class' => 'site_url brand-list__item--link'
            ];
        }

		return app()->chunk()->set('items', $links)->render('common.links_list');
	}

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
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
			'firm_id' => [
				'col' => [
					'flags' => 'not_null key unsigned',
					'type' => 'int_2'
				],
				'elem' => 'hidden_field',
				'label' => 'ID фирмы',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_city' => [
				'elem' => 'hidden_field',
				'label' => 'ID города (старый)',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_firm_user' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID пользователя',
				'params' => [
					'rules' => ['int']
				]
			],
			'company_name_jure' => [
				'elem' => 'text_field',
				'label' => 'Юридическое название',
				'params' => [
					'rules' => ['length' => array('max' => 200, 'min' => 1)]
				]
			],
			'flag_is_price_attached' => [
				'elem' => 'single_check_box',
				'label' => 'Прикрепить основной прайс',
				'default_val' => 0
			],
			'company_map_address' => [
				'elem' => 'text_field',
				'label' => 'Адрес на карте',
				'params' => [
					'rules' => ['length' => array('max' => 300, 'min' => 1)]
				]
			],
			'mode_work' => [
				'elem' => 'text_field',
				'label' => 'Режим работы',
				'params' => [
					'rules' => ['length' => array('max' => 500, 'min' => 1)]
				]
			],
			'company_fax' => [
				'elem' => 'text_field',
				'label' => 'Факс',
				'params' => [
					'rules' => ['length' => array('max' => 64, 'min' => 1)]
				]
			],
            'company_viber' => [
				'elem' => 'text_field',
				'label' => 'Viber',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_viber' => [
				'elem' => 'text_field',
				'label' => 'Viber',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_whatsapp' => [
				'elem' => 'text_field',
				'label' => 'WhatsApp',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_skype' => [
				'elem' => 'text_field',
				'label' => 'Skype',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_telegram' => [
				'elem' => 'text_field',
				'label' => 'Telegram',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_vk' => [
				'elem' => 'text_field',
				'label' => 'Вконтакте',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_fb' => [
				'elem' => 'text_field',
				'label' => 'Facebook',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
            'company_in' => [
				'elem' => 'text_field',
				'label' => 'Instagram',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
			'path' => [
				'elem' => 'text_field',
				'label' => 'Как проехать',
				'params' => [
					'rules' => ['length' => array('max' => 500, 'min' => 1)]
				]
			],
			'rating' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'double(3,2)'
				],
				'elem' => 'text_field',
				'label' => 'Рейтинг',
				'params' => [
					'rules' => ['double']
				]
			],
			'priority' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'Приоритет',
				'params' => [
					'rules' => ['int']
				]
			],
			'random_value' => $c->intField('Рандом'),
		];
	}

	public function name() {
		return $this->val('company_name');
	}

	public function isBlocked() {
		return (int)$this->val('flag_is_active') !== 1;
	}

	public function linkItem() {
		return '/firm/show/'.$this->id_firm().'/'.$this->id_service().'/'.$this->id().'/';
	}

	public function linkPricelist($id_catalog = null) {
		$params = ['mode' => 'price'];
		if ($id_catalog !== null) {
			$params['id_catalog'] = (int)$id_catalog;
		}
		return app()->linkFilter('/firm/show/'.$this->id_firm().'/'.$this->id_service().'/'.$this->val('id'), $params);
	}

	public function logoPath() {
		return $this->firm()->hasLogo() ? $this->firm()->val('file_logo') : '/css/img/firm_logo.png';
	}

	public function description() {
		$order = array("\r\n", "\n", "\r");
		$text = $this->val('text');

		$id_firm = $this->id_firm();
		$id_service = $this->id_service();
		// Если не 10 служба - убираем вообще все ссылки
		if ($id_service != 10) {
			$text = preg_replace_callback('~<a.*?>.*?</a>~u', function() {
				return '';
			}, $text);
		}
		$text = str()->replace(str()->replace($text, ' target="_blank"', ''), ' rel="nofollow"', '');
		// Для 10 службы преобразовываем прямые ссылки
		$text = preg_replace_callback('~href.*?=.*?"([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
			if (substr(trim($matches[1]), 0, strlen('mailto')) !== 'mailto') {
				return 'target="_blank" rel="nofollow" href="'.app()->away(trim($matches[1]), $id_firm).'"';
			} else {
				return trim($matches[1]);
			}
		}, $text);

		return str()->replace(str()->replace($text, $order, '<br/>'), '\\', '');
	}

	public function hasAbout() {
		if ($this->about_text === null) {
			$this->about_text = $this->about();
		}

		return ($this->about_text !== null && $this->about_text !== false && str()->length($this->about_text) > 0);
	}

	public function about() {
		if ($this->about_text === null) {
			$order = array("\n");
			$firm_description = new FirmDescription();
			$firm_description->getByFirm($this);

			if ($firm_description->exists()) {
				$text = $firm_description->val('text');

				$id_firm = $this->id_firm();
				$id_service = $this->id_service();
				$text = str()->replace(str()->replace($text, ' target="_blank"', ''), ' rel="nofollow"', '');
				$text = preg_replace_callback('~href.*?=.*?"([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
					if (substr(trim($matches[1]), 0, strlen('mailto')) !== 'mailto') {
						return 'target="_blank" rel="nofollow" href="'.trim($matches[1]).'"';
					} else {
						return trim($matches[1]);
					}
				}, $text);

				$html_mode = str()->index($text, '<p>');
				if ($html_mode !== false) {
					$this->about_text = $text;
				} else {
					$this->about_text = '<p>'.str()->replace(str()->replace($text, $order, '</p><p>').'</p>', '\\', '');
				}
			} else {
				$this->about_text = false;
			}
		}

		return $this->about_text;
	}

	public function hasDescription() {
		return str()->length(trim($this->val('text'))) > 2;
	}

	public function hasLongDescription() {
		return str()->length(trim($this->val('text'))) > 300;
	}

	public function activity() {
		$result = '';
		if ($this->id_service() === 10) {
			$result = nl2br($this->val('company_activity'));
		} else {
			$result = str()->toLower($this->val('company_activity'));
			$sentences = preg_split('~\. ~', $result);
			$result = '';
			foreach ($sentences as $sentence) {
				$result .= (empty($result) ? '' : '. ').str()->firstCharToUpper($sentence);
			}

			$result = nl2br($result);
		}

		return $result;
	}

	public function hasActivity() {
		return str()->length($this->val('company_activity')) > 2;
	}

	public function hasModeWork() {
		return str()->length($this->val('mode_work')) > 2;
	}

	public function modeWork() {
		$this->setVal('mode_work', str()->replace($this->val('mode_work'), "\\", ""));
		return $this->id_service() === 10 ? $this->val('mode_work') : str()->firstCharToUpper(str()->toLower($this->val('mode_work')));
	}

	public function hasPath() {
		return str()->length($this->val('path')) > 4;
	}

	public function hasPhone() {
		return str()->length($this->companyDataComponent()->val('phone')) > 4;
	}

	public function hasWeb() {
		return str()->length($this->companyDataComponent()->val('web_site_url')) > 4;
	}

	public function priority() {
		return $this->val('priority') ? (int)$this->val('priority') : 0;
	}

	public function path() {
		return $this->id_service() === 10 ? $this->val('path') : str()->firstCharOfSentenceToUpper(str()->toLower($this->val('path')));
	}

	public function phone() {
		return self::formatPhoneFax($this->companyDataComponent()->val('phone'));
	}

	public function webSiteUrls() {
		return array_filter(preg_split('~[;,]~', str_replace(' ', '', $this->companyDataComponent()->val('web_site_url'))));
	}

	public function webSiteMain() {
		return $this->webSiteUrls()[0] ?? APP_URL.$this->linkItem();
	}

	public function hasFax() {
		return str()->length($this->companyDataComponent()->val('fax')) > 4;
	}

	public function fax() {
		return self::formatPhoneFax($this->companyDataComponent()->val('fax'));
	}

	public function hasEmail() {
		return str()->length($this->companyDataComponent()->val('email')) > 5;
	}
    
    public function tEmail() {
        return '<a rel="nofollow" class="email fancybox fancybox.ajax" title="Email" href="/firm-feedback/get-feedback-form/' . $this->id_firm() . '/' . $this->id_service() . '/"></a>';
    }
        
    public function hasViber() {
		return str()->length($this->val('company_viber')) > 4;
	}

    public function viber() {
		return '<a rel="nofollow" class="viber" title="Viber" href="viber://add?number=:' . trim($this->val('company_viber')) . '"></a>';
	}

    public function hasWhatsApp() {
		return str()->length($this->val('company_whatsapp')) > 4;
	}

    public function whatsapp() {
		return '<a rel="nofollow" class="whatsapp" title="WhatsApp" href="whatsapp://send?phone=' . trim($this->val('company_whatsapp')) . '"></a>';
	}

    public function hasSkype() {
		return str()->length($this->val('company_skype')) > 3;
	}

    public function skype() {
		return '<a rel="nofollow" class="skype" title="Skype" href="skype:' . trim($this->val('company_skype')) . '?chat"></a>';
	}

    public function hasTelegram() {
		return str()->length($this->val('company_telegram')) > 3;
	}

    public function telegram() {
		return '<a rel="nofollow" class="telegram" title="Telegram" href="tg://resolve?domain=' . trim($this->val('company_telegram')) . '"></a>';
	}

    public function hasVkontakte() {
		return str()->length($this->val('company_vk')) > 3;
	}

    public function vkontakte() {
		return '<a target="_blank" rel="nofollow" class="vkontakte" title="Vkontakte" href="' . trim($this->val('company_vk')) . '"></a>';
	}

    public function hasFacebook() {
		return str()->length($this->val('company_fb')) > 3;
	}

    public function facebook() {
		return '<a target="_blank" rel="nofollow" class="facebook" title="Facebook" href="' . trim($this->val('company_fb')) . '"></a>';
	}

    public function hasInstagram() {
		return str()->length($this->val('company_in')) > 3;
	}

    public function instagram() {
		return '<a target="_blank" rel="nofollow" class="instagram" title="Instagram" href="' . trim($this->val('company_in')) . '"></a>';
	}
    
    public function hasMessengers() {
		return $this->hasEmail() || $this->hasViber() || $this->hasWhatsApp() || $this->hasSkype() || $this->hasTelegram();
	}

    public function hasSocialNetworks() {
		return $this->hasVkontakte() || $this->hasFacebook() || $this->hasInstagram();
	}
    
    public function hasPriceList() {
		$res = false;

		$pcp = new PriceCatalogPrice();
		$pcp->getByIdFirm($this->id());

        $price = new Price();
        $price->getByIdFirm($this->id());
        
		if ($pcp->exists() || $price->exists()) {
			$res = true;
		}

		return $res;
	}

	public function hasRegionCity() {
		return (int)$this->val('id_region_city') > 1;
	}

	public function canSell() {
		return str()->length($this->companyDataComponent()->val('email')) > 5;
	}

	public function email() {
		return trim($this->companyDataComponent()->val('email'));
	}

	public function firstEmail() {
		$emails = preg_split("/[;, ]+/", $this->companyDataComponent()->val('email'));
		foreach($emails as $email) {
            if (strpos($email, '@') !== FALSE)
                return trim($email);
		}
        return '';
	}

	public function emailAddresses() {
		return preg_split("/[;,]+/", $this->companyDataComponent()->val('email'));
	}

	public function hasAddress() {
		return str()->length($this->companyDataComponent()->val('address')) > 5;
	}

	public function address() {
		return $this->getGeoAddress();
	}

	public function addressWithProps() {
		$geoData = $this->geoDataComponent()->getGeoData();
		$addrString = trim(str()->firstCharToUpper(str()->toLower($geoData['country']))).', '.trim(str()->firstCharToUpper(str()->toLower($geoData['region']))).', ';
		$addrString .= '<span itemprop="addressLocality">';

		$order = array("\r\n", "\n", "\r");
		$_address = $this->companyDataComponent()->val('address');
		$_address = preg_replace_callback('~<a.*?>.*?</a>~u', function() {
			return '';
		}, $_address);
		$_address = str()->replace(str()->replace($_address, $order, ''), '\\', '');

		if ($geoData['cityType'] == 19) {
			$addrString .= 'г. ';
		} else {
			$cityType = app()->db()->query()
					->setSelect(['name'])
					->setFrom(['sts_city_type'])
					->setWhere('`id_city_type` = :city_type', [':city_type' => $geoData['cityType']])
					->selectRow();

			$addrString .= trim(str()->firstCharToUpper(str()->toLower($cityType['name']))).' ';
		}

		$result_address = $addrString.trim(str()->firstCharsOfWordsToUpper(str()->toLower($geoData['city']))).'</span>';

		if ($this->id_service() != 10) {
			$addrString .= trim(str()->firstCharsOfWordsToUpper(str()->toLower($geoData['city'])).'</span>, <span itemprop="streetAddress">'.trim(str()->firstCharsOfWordsToUpper(str()->toLower($_address))).'</span>');
		} else {
			$addrString .= trim(str()->firstCharToUpper(str()->toLower($geoData['city']))).'</span>, <span itemprop="streetAddress">'.trim($_address).'</span>';
		}

		$addrString = str()->replace($addrString, 'Д.', 'д.');
		$addrString = str()->replace($addrString, 'Ул.', 'ул.');

		return $_address ? $addrString : $result_address;
	}

	public function regionCity() {
		$result = '';

		$sts_region_city = new StsRegionCity();
		$sts_region_city->reader()
				->setWhere(['AND', '`id_region_city` = :id_region_city', '`id_city` = :id_city'], [':id_region_city' => $this->val('id_region_city'), ':id_city' => 76004])
				->objectByConds();

		if ($sts_region_city->exists()) {
			$result = $sts_region_city->val('name').' район';
		}

		return $result;
	}

	public function shortAddress() {
		return $this->getGeoAddress(true);
	}

	public function getByIdFirmAndIdService($id_firm, $id_service) {
		$this->reader()
				->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service'], [':id_firm' => (int)$id_firm, ':id_service' => (int)$id_service])
				->objectByConds();
		return $this;
	}

	public function getTotalPriceListCount() {
		$pcp = new Price();
		return $pcp->reader()->setSelect('COUNT(*) as `count`')
						->setWhere(['AND', 'id_firm = :id_firm', 'flag_is_active = :active'], [':id_firm' => $this->id(), ':active' => 1])
						->rowByConds()['count'];
	}

	public function getTypes() {
		$ft = new FirmType();
		return $ft->getByFirm($this);
	}

	private function formatPhoneFax($string) {
		$string = str()->sentance(str()->toLower($string));
		$geo_data = $this->geoDataComponent()->getGeoData();

		$region_code = $geo_data['cityCode'];
		$country_code = $geo_data['countryCode'];

		//в телефоне может присутствовать название города (поправить заглавную букву города)
		$string = str()->replace($string, 'рыбинск', 'Рыбинск');
		$string = str()->replace($string, 'костром', 'Костром');
		$string = str()->replace($string, ' рф', ' РФ');

		//также если в телефоне присутствует код города
		if (preg_match('~^[0-9 -]{4,11}([., \-][^0-9]|$)~', $string) and $region_code) $string = '+'.($country_code == '0' ? 7 : $country_code).' ('.$region_code.') '.$string;

		return $string;
	}

	private function getGeoAddress($short = false) {
		$geoData = $this->geoDataComponent()->getGeoData();
		$addrString = '';
		$order = array("\r\n", "\n", "\r");
		$_address = $this->companyDataComponent()->val('address');
		$_address = preg_replace_callback('~<a.*?>.*?</a>~u', function() {
			return '';
		}, $_address);
		$_address = str()->replace(str()->replace($_address, $order, ''), '\\', '');

		if (!$short) {
			$addrString = trim(str()->firstCharToUpper(str()->toLower($geoData['country']))).', '.trim(str()->firstCharToUpper(str()->toLower($geoData['region']))).', ';
		}

		if ($geoData['cityType'] == 19) {
			$addrString .= 'г. ';
		} else {
			$cityType = app()->db()->query()
					->setSelect(['name'])
					->setFrom(['sts_city_type'])
					->setWhere('`id_city_type` = :city_type', [':city_type' => $geoData['cityType']])
					->selectRow();

			$addrString .= trim(str()->firstCharToUpper(str()->toLower($cityType['name']))).' ';
		}

		$result_address = $addrString.trim(str()->firstCharsOfWordsToUpper(str()->toLower($geoData['city'])));
		if ($this->id_service() != 10) {
			$addrString .= trim(str()->firstCharsOfWordsToUpper(str()->toLower($geoData['city'])).', '.trim(str()->firstCharsOfWordsToUpper(str()->toLower($_address))));
		} else {
			$addrString .= trim(str()->firstCharToUpper(str()->toLower($geoData['city']))).', '.trim($_address);
		}

		$addrString = str()->replace($addrString, 'Д.', 'д.');
		$addrString = str()->replace($addrString, 'Ул.', 'ул.');

		return $_address ? $addrString : $result_address;
	}

	public function getCity() {
		$geoData = $this->geoDataComponent()->getGeoData();
		return str()->firstCharsOfWordsToUpper(str()->toLower($geoData['city']));
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Фирма';
	}

	public function getWhereConds() {
		return [
			'where' => [
				'AND',
				'id_firm = :id_firm',
			],
			'params' => [
				':id_firm' => $this->id()
			]
		];
	}

/*	public function insert($vals = null, $parent_object = null) {
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
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		if ((int)$this->val('flag_is_active') === 0) {
			$sphinx->delete()
					->from(SPHINX_FIRM_INDEX)
					->where('id', '=', (int)$this->id())
					->execute();
		} else {
			$district_name = (new StsRegionCity($this->val('id_region_city')))->val('name');
			$timestamp_inserting = new \Sky4\Helper\DateTime($this->val('timestamp_inserting'));
			$timestamp_last_updating = new \Sky4\Helper\DateTime($this->val('timestamp_last_updating'));

			$row = [
				'id' => $this->id(),
				'company_activity' => $this->val('company_activity'),
				'company_address' => $this->val('company_address'),
				'company_email' => $this->val('company_email'),
				'company_fax' => $this->val('company_fax'),
				'company_map_address' => $this->val('company_map_address'),
				'company_name' => $this->val('company_name'),
				'company_name_jure' => $this->val('company_name_jure'),
				'company_name_ratiss' => $this->val('company_name_ratiss'),
				'company_phone' => $this->val('company_phone'),
				'company_web_site_url' => $this->val('company_web_site_url'),
				'company_viber' => $this->val('company_viber',''),
				'company_whatsapp' => $this->val('company_whatsapp',''),
				'company_skype' => $this->val('company_skype',''),
				'company_telegram' => $this->val('company_telegram',''),
				'company_vk' => $this->val('company_vk',''),
				'company_fb' => $this->val('company_fb',''),
				'company_in' => $this->val('company_in',''),
				'district_name' => $district_name,
				'mode_work' => $this->val('mode_work'),
				'text' => $this->val('text'),
				'sortname' => $this->val('company_name'),
				'flag_is_active' => $this->val('flag_is_active'),
				'flag_is_producer' => $this->val('flag_is_producer'),
				'id_contract' => $this->val('id_contract'),
				'id_firm_user' => $this->val('id_firm_user'),
				'id_firm' => $this->val('id_firm'),
				'id_region_city' => $this->val('id_region_city'),
				'id_service' => $this->val('id_service'),
				'id_manager' => $this->val('id_manager'),
				'id_parent' => $this->val('id_parent'),
				'priority' => $this->val('priority'),
				'rating' => $this->val('rating'),
				'random_value' => rand(1, 1000),
				'timestamp_inserting' => $timestamp_inserting->timestamp(),
				'timestamp_last_updating' => $timestamp_last_updating->timestamp()
			];

			$sphinx->replace()
					->into(SPHINX_FIRM_INDEX)
					->set($row)
					->execute();
		}

		return $this;
	}*/

}
