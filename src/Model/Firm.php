<?php

namespace App\Model;

class Firm extends \Sky4\Model\Composite {

	private $about_text = null;
	private $firm_gallery = null;
	private $firm_files = null;
	private $firm_delivery = null;
    
    private $firm_branches = null;
    private $firm_branches_count = null;
    public $branch_id = null;
    public $flag_is_price_attached = null;

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

	public function defaultEyeEnabled() {
		return true;
	}

	public function cols() {
		return [
			'id',
			'company_name',
			'dopcontent',
			'idfirmidservice' => [
				'label' => 'id_firm/id_service',
				'name' => 'renderLegacyCol',
				'type' => 'method'
			],
			'recount' => [
				'label' => 'Действия',
				'name' => 'renderRecountCol',
				'type' => 'method'
			],
			'reprice' => [
				'label' => 'Дубли в прайсе',
				'name' => 'renderRepriceCol',
				'type' => 'method'
			],
        ] + $this->activeComponent()->cols();
	}

	public function filterFields() {
		return [
			'company_name' => ['elem' => 'text_field', 'label' => 'Название фирмы', 'field_name' => 'company_name'],
			'id_firm' => ['elem' => 'text_field', 'label' => 'ID_FIRM', 'field_name' => 'id_firm'],
			'id_service' => ['elem' => 'text_field', 'label' => 'ID_SERVICE', 'field_name' => 'id_service'],
		];
	}

	public function filterFormStructure() {
		$fs = $this->formStructureCreator();
		return [
			$fs->field('company_name'),
			$fs->field('id_firm'),
			$fs->field('id_service')
		];
	}

	public function renderLegacyCol() {
		return $this->val('id_firm').'/'.$this->val('id_service');
	}

	public function renderRecountCol() {
		return $this->activeComponent()->isActive() ? '<a href="/utils/recount-firm/?mode=deactivate&id_firm='.$this->id().'" target="_blank">деактивировать</a>' : '<a href="/utils/recount-firm/?mode=activate&id_firm='.$this->id().'" target="_blank">активировать</a>';
	}
    
    public function renderRepriceCol() {
		return '<a href="/utils/check-firm-price/?id_firm='.$this->id().'" target="_blank">Привязка картинок</a>';
	}

	public function renderPhoneLinks() {
		$links = [];
		$phones = explode(', ', $this->val('company_phone'));
		$geo_data = $this->geoDataComponent()->getGeoData();

		$region_code = $geo_data['cityCode'];
		$country_code = $geo_data['countryCode'];
		foreach ($phones as $phone) {
			$prefix = str()->sub(trim($phone), 0, 2);
			if ($prefix === '8 ' || $prefix === '8-') {
				$links[] = $this->getCellPhone(trim($phone), true, $this);
			} else {
				$phone = str()->replace($phone, 'рыбинск', 'Рыбинск');
				$phone = str()->replace($phone, 'ростов', 'Ростов');
				$phone = str()->replace($phone, 'костром', 'Костром');
				$phone = str()->replace($phone, ' рф', ' РФ');
                if ($region_code!=800){
                    $phone = '+'.($country_code == '0' ? 7 : $country_code).' ('.$region_code.') '.$phone;
                    $href = 'tel:+'.preg_replace('~[^0-9]~', '', $phone);
                }else{
                    //$phone = '8 ('.$region_code.') '.$phone;
                    $phone = '8 (800) '.substr(preg_replace('~[^0-9]~', '', $phone), 4, 7);
                    $href = 'tel:'.preg_replace('~[^0-9]~', '', $phone);
                    
                }
				
				$links[] = [
					'href' => $href,
					'name' => trim($phone),
					'class' => 'tel brand-list__item--link',
                    'data-firm-id' => $this->id()
				];
			}
		}

		/*if ($this->hasCellPhone()) {
			$links[] = $this->getCellPhone($this->val('company_cell_phone'));
		}*/

		$_links = [];
		foreach ($links as $link) {
			$_links[$link['href']] = $link;
		}

		return app()->chunk()->set('items', $_links)->render('common.links_list');
	}

	private static function getCellPhone($phone, $eight_mode = false, $firm = false) {
		$phone = preg_replace('~[^0-9]~u', '', $phone);
		if ($eight_mode) {
            if (substr(str()->sub($phone, 1), 0, 3)!='800'){
                $phone = '+7'.str()->sub($phone, 1);
            }else{
                $phone = ' 8'.str()->sub($phone, 1);
            }
			
		} else {
            if (substr($phone, 1, 3)!='800'){
                $phone = '+'.$phone;
            }else{
                $phone = $phone;
            }
			//$phone = '+'.$phone;
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
                'data-firm-id' => $firm->id()
			];
	}

	public function renderWebLinks() {
		$links = [];
		if ($this->hasWebPartner()) {
			foreach ($this->webSiteUrls() as $url) {

				$links[] = [
					'target' => '_blank',
					'href' => '/page/away/firm/'.$this->id().'/',
					'rel' => 'nofollow',
					'name' => trim($url),
					'class' => 'site_url brand-list__item--link'
				];
			}
		} else {
			foreach ($this->webSiteUrls() as $url) {
				$links[] = [
					'target' => '_blank',
					'href' => app()->away($url, $this->id()),
					'rel' => 'nofollow',
					'name' => trim($url),
					'class' => 'site_url brand-list__item--link'
				];
			}
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
			'id_city' => [
				'elem' => 'hidden_field',
				'label' => 'ID города (старый)',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_parent' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID головного офиса',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_description' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID описания',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_contract' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID договора',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_manager' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID менеджера',
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
			'company_name_ratiss' => [
				'elem' => 'text_field',
				'label' => 'Имя Ратисс',
				'params' => [
					'rules' => ['length' => array('max' => 128, 'min' => 1)]
				]
			],
			'dopcontent' => [
				'elem' => 'text_field',
				'label' => 'dopcontent'
			],
			'company_name_jure' => [
				'elem' => 'text_field',
				'label' => 'Юридическое название',
				'params' => [
					'rules' => ['length' => array('max' => 200, 'min' => 1)]
				]
			],
			'company_cell_phone' => [
				'elem' => 'text_field',
				'label' => 'Сотовый телефон для сообщений',
				'params' => [
					'rules' => ['length' => array('max' => 30, 'min' => 1)]
				]
			],
			'flag_is_producer' => [
				'elem' => 'single_check_box',
				'label' => 'Производитель',
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
			'company_phone_readdress' => [
				'elem' => 'text_field',
				'label' => 'Телефон переадресации',
				'params' => [
					'rules' => ['length' => array('max' => 64, 'min' => 1)]
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
			'file_logo' => [
				'elem' => 'text_field',
				'label' => 'Путь до логотипа',
				'params' => [
					'rules' => ['length' => array('max' => 100, 'min' => 1)]
				]
			],
			'file_description' => [
				'elem' => 'text_field',
				'label' => 'Путь до файла с описанием',
				'params' => [
					'rules' => ['length' => array('max' => 100, 'min' => 1)]
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
			'timestamp_ratiss_updating' => [
				'elem' => 'date_time_field',
				'label' => 'Время последнего изменения из РАТИСС'
			],
			'web_site_partner_url' => $c->stringField('Партнерский url', 1024)
		];
	}

	public function name() {
		return $this->val('company_name');
	}

	public function id_manager() {
		if ($this->val('id_manager')) {
			return (int)$this->val('id_manager');
		}

		return null;
	}

	public function isBlocked() {
		return (int)$this->val('flag_is_active') !== 1;
	}

	public function hasLogo() {
		return $this->val('file_logo') ? true : false;
	}

	public function linkItem() {
        if ($this->isBranch()) {
            return '/firm/show/'.$this->val('id_firm').'/'.$this->val('id_service').'/'.$this->branch_id.'/';
        } else {
            return '/firm/show/'.$this->val('id_firm').'/'.$this->val('id_service').'/';
        }
	}

	public function linkPricelist($id_catalog = null) {
		$params = ['mode' => 'price'];
		if ($id_catalog !== null) {
			$params['id_catalog'] = (int)$id_catalog;
		}
        if ($this->isBranch()) {
    		return app()->linkFilter('/firm/show/'.$this->id_firm().'/'.$this->id_service().'/'.$this->branch_id.'/', $params);
        } else {
    		return app()->linkFilter('/firm/show/'.$this->id_firm().'/'.$this->id_service().'/', $params);
        }
	}

	public function logoPath() {
		return $this->hasLogo() ? $this->val('file_logo') : '/css/img/firm_logo.png';
	}
    public function dopcontent() {
        return $this->val('dopcontent');
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

        $text = str()->replace($text, $order, '<br/>');
		return str()->replace($text, '\\', '');
	}
    
    private function renderDescription() {
        $text = $this->description();
        $text = explode('!!!РАЗДЕЛИТЕЛЬ_БЛОКА!!!',$text);
        return $text[0];
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
		return str()->length(trim($this->renderDescription())) > 2;
	}

	public function hasGallery() {
		$this->getGallery();
		return count($this->firm_gallery) > 0;
	}

	public function getGallery() {
		if ($this->firm_gallery === null) {
			$this->firm_gallery = FirmFile::getImagesByFirm($this);
		}

		return $this->firm_gallery;
	}

	public function hasDelivery() {
		$this->getDelivery();
		return (isset($this->firm_delivery['text']) && $this->firm_delivery['text']) || (isset($this->firm_delivery['types']) && $this->firm_delivery['types']);
	}

	public function getDelivery() {
		if ($this->firm_delivery === null) {
			$fd = new FirmDelivery();
			$fd->getByFirm($this);
			if ($fd->exists()) {
				$types = FirmDelivery::types();
				$d_types = explode(',', $fd->val('type'));
				$d_types = array_filter($d_types);
				$result = [];
				foreach ($d_types as $type) {
					$result['types'][] = $types[$type];
				}
				$result['text'] = $fd->val('text');

				$this->firm_delivery = $result;
			}
		}

		return $this->firm_delivery;
	}

	public function hasFiles() {
		if ($this->firm_files === null) {
			$this->firm_files = FirmFile::getFilesByFirm($this);
		}

		return count($this->firm_files) > 0;
	}

	public function getFiles() {
		if ($this->firm_files === null) {
			$this->firm_files = FirmFile::getFilesByFirm($this);
		}

		return $this->firm_files;
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

	public function hasCellPhone() {
		return str()->length($this->val('company_cell_phone')) > 5;
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

	public function hasWebPartner() {
		return str()->length($this->val('web_site_partner_url')) > 4;
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

	public function cellPhone() {
		return self::formatPhoneFax($this->val('company_cell_phone'));
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
		return '<a rel="nofollow" class="viber brand-list__item--link" title="Viber" href="viber://add?number=:' . trim($this->val('company_viber')) . '"></a>';
	}

    public function hasWhatsApp() {
		return str()->length($this->val('company_whatsapp')) > 4;
	}

    public function whatsapp() {
		return '<a rel="nofollow" class="whatsapp brand-list__item--link" title="WhatsApp" href="whatsapp://send?phone=' . trim($this->val('company_whatsapp')) . '"></a>';
	}

    public function hasSkype() {
		return str()->length($this->val('company_skype')) > 3;
	}

    public function skype() {
		return '<a rel="nofollow" class="skype brand-list__item--link" title="Skype" href="skype:' . trim($this->val('company_skype')) . '?chat"></a>';
	}

    public function hasTelegram() {
		return str()->length($this->val('company_telegram')) > 3;
	}

    public function telegram() {
		return '<a rel="nofollow" class="telegram brand-list__item--link" title="Telegram" href="tg://resolve?domain=' . trim($this->val('company_telegram')) . '"></a>';
	}

    public function hasVkontakte() {
		return str()->length($this->val('company_vk')) > 3;
	}

    public function vkontakte() {
		return '<a target="_blank" rel="nofollow" class="vkontakte brand-list__item--link" title="Vkontakte" href="' . trim($this->val('company_vk')) . '"></a>';
	}

    public function hasFacebook() {
		return str()->length($this->val('company_fb')) > 3;
	}

    public function facebook() {
		return '<a target="_blank" rel="nofollow" class="facebook brand-list__item--link" title="Facebook" href="' . trim($this->val('company_fb')) . '"></a>';
	}

    public function hasInstagram() {
		return str()->length($this->val('company_in')) > 3;
	}

    public function instagram() {
		return '<a target="_blank" rel="nofollow" class="instagram brand-list__item--link" title="Instagram" href="' . trim($this->val('company_in')) . '"></a>';
	}
    
    public function hasMessengers() {
		return $this->hasViber() || $this->hasWhatsApp() || $this->hasSkype() || $this->hasTelegram();
	}

    public function hasSocialNetworks() {
		return $this->hasVkontakte() || $this->hasFacebook() || $this->hasInstagram();
	}
    
    public function hasPriceList() {
		$res = false;
        
        if ($this->isBranch() && !$this->isBranchPriceAttached()) {
            return false;
        }

		$pcp = new PriceCatalogPrice();
		$pcp->getByIdFirm($this->id());

        $price = new Price();
        $price->getByIdFirm($this->id());
        
		if ($pcp->exists() || $price->exists()) {
			$res = true;
		}

		return $res;
	}

	public function hasVideo() {
		$res = false;
		$fv = new FirmVideo();
		$fv->getByIdFirm($this->id());

		if ($fv->exists()) {
			$res = true;
		}

		return $res;
	}

	public function hasPromo() {
		$fp = new FirmPromo();
		$fp_where = [
			'AND',
			//['OR',
			//'`flag_is_infinite` = :flag',
			['AND', /* '`timestamp_beginning`< :now', */ 'timestamp_ending > :now'],
			//],
			['AND', 'flag_is_active = :flag', '`id_firm` = :id_firm']
		];
		$count = $fp->reader()
				->setWhere($fp_where, [':id_firm' => $this->id(), ':flag' => 1, ':now' => \Sky4\Helper\DeprecatedDateTime::now()])
				->count();


		return $count > 0;
	}

	public function hasRegionCity() {
		return (int)$this->val('id_region_city') > 1;
	}

	public function hasReviews() {
		$fr = new FirmReview();
		return $fr->reader()
						->setWhere(['AND', 'id_firm = :id_firm', 'flag_is_active = :flag_is_active'], [':id_firm' => $this->id(), ':flag_is_active' => 1])
						->count() > 0;
	}
    
    public function isBranch() {
        return $this->branch_id !== null;
    }
    
    public function isBranchPriceAttached() {
        if ($this->branch_id !== null && $this->flag_is_price_attached == 1) {
            return true;
        }
        
        return false;
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

		if ($this->val('id_service') != 10) {
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
				->setWhere(['AND', '`id_region_city` = :id_region_city', '`id_city` = :id_city'], [':id_region_city' => $this->val('id_region_city'), ':id_city' => $this->val('id_city')])
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

	/**
	 * 
	 * @return Firm[]
	 */
	public function getBranches() {
		if ((int)$this->val('id_parent') > 0) {
			$where = [
				'AND',
				['OR', '`id_firm` = :id_parent', '`id_parent` = :id_parent'],
				'`id_service` = :id_service',
				'`id` != :id',
				'`flag_is_active` = :yes'
			];

			$params = [
				':id_parent' => $this->val('id_parent'),
				':id_service' => $this->val('id_service'),
				':id' => $this->id(),
				':yes' => 1
			];
		} else {
			$where = [
				'AND',
				'`id_parent` = :id_firm',
				'`id_service` = :id_service',
				'`flag_is_active` = :yes'
			];

			$params = [
				':id_firm' => $this->val('id_firm'),
				':id_service' => $this->val('id_service'),
				':yes' => 1
			];
		}

		return $this->reader()
						->setWhere($where, $params)
						->setOrderBy('`id_parent` ASC')
						->objects();
	}
    
    public function isForCurrentCity() {
        if (in_array($this->val('id_city'), app()->location()->getCityIds())) {
            return true;
        }
        
        return false;
    }
    
    public function getCityFirmBranches() {
        $_firm_branches = new FirmBranch();
        $cities_where_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
        $where = [
            'AND',
            '`firm_id` = :firm_id',
            '`flag_is_active` = :yes',
            $cities_where_conds['where']
        ];

        $params = array_merge(
            [
                ':firm_id' => $this->id(),
                ':yes' => 1
            ], 
            $cities_where_conds['params']
        );

		return $_firm_branches->reader()
						->setWhere($where, $params)
						//->setOrderBy('`id` ASC')
						->objects();
	}
    
    //Получаем филиалы с группировкой по городу
    public function getFirmBranches($order_by_city = true) {
        if ($this->firm_branches === null) {
            $where = [
                'AND',
                '`firm_id` = :firm_id',
                '`flag_is_active` = :yes'
            ];

            $params = array_merge(
                [
                    ':firm_id' => $this->id(),
                    ':yes' => 1
                ]
            );

            $_fb = new FirmBranch();
            $_firm_branches = [];
            if ($order_by_city) {
                $_id_city = app()->location()->getCityId();
                $_id_region_city = app()->location()->getRegionId();
                $_firm_branches = $_fb->reader()
                                ->setWhere($where, $params)
                                ->setOrderBy('FIELD(`id_city`, ' . ((int)$_id_city ? (int)$_id_city : 76004) . ') DESC, FIELD(`id_region_country`, ' . ((int)$_id_region_city ? (int)$_id_region_city : 76) . ') DESC')
                                ->objects();
            } else {
                $_firm_branches = $_fb->reader()
                                ->setWhere($where, $params)
                                ->objects();
            }

            foreach($_firm_branches as $firm_branch) {
                if (!isset($this->firm_branches[$firm_branch->val('id_city')])) {
                    $this->firm_branches[$firm_branch->val('id_city')] = [];
                }

                $this->firm_branches[$firm_branch->val('id_city')] []= $firm_branch;
            }
        }
        
		return $this->firm_branches;
	}
    
    //Получаем филиалы с группировкой по городу
    public function getFirmBranchesCount() {
        if ($this->firm_branches === null) {
            $where = [
                'AND',
                '`firm_id` = :firm_id',
                '`flag_is_active` = :yes'
            ];

            $params = array_merge(
                [
                    ':firm_id' => $this->id(),
                    ':yes' => 1
                ]
            );

            $_fb = new FirmBranch();
            $this->firm_branches_count = $_fb->reader()
                            ->setWhere($where, $params)
                            ->count();
        }
        
		return $this->firm_branches_count;
	}
    
    //Получаем филиалы с группировкой по городу
    public function hasFirmBranches() {
		return $this->getFirmBranchesCount() > 0 ? true : false;
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
		if ($this->val('id_service') != 10) {
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

	public function suggest($q, $field_name = 'name', $rel_fields = array()) {
		$q = str()->trim($q);
		$field_name = (string)$field_name;
		$result = [];
		if ($q && $field_name) {
			$id_fields_names = $this->idFieldsNames();
			if ($q && is_array($id_fields_names) && (count($id_fields_names) === 1) && isset($id_fields_names[0]) && isset($this->vals[$field_name])) {
				$_select = array('`'.$id_fields_names[0].'` AS `key`', 'CONCAT(`'.$field_name.'`," [",`id_firm`,"/",`id_service`,"]") AS `val`');
				$_where = ['AND', '`'.$field_name.'` LIKE :'.$field_name];
				$_params = array(':'.$field_name => ''.$q.'%');
				foreach ($rel_fields as $rel_field_name => $rel_field_val) {
					$_where[] = '`'.$rel_field_name.'` = :'.$rel_field_name;
					$_params[':'.$rel_field_name] = $rel_field_val;
				}
				$result = $this->reader()
						->setSelect($_select)
						->setWhere($_where, $_params)
						->setLimit(20)
						->rows();
			}
		}

		return $result;
	}

	public function getReviewsCount() {
		$fr = new FirmReview();
		$where = [
			'AND',
			'`id_firm` = :id_firm',
			'`flag_is_active` = :flag_is_active'
		];

		$params = [
			':id_firm' => $this->id(),
			':flag_is_active' => 1
		];

		return $fr->reader()
						->setWhere($where, $params)
						->count();
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
				'company_phone_readdress' => $this->val('company_phone_readdress'),
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
	}

}
