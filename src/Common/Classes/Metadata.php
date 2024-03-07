<?php

namespace App\Classes;

use App\Model\Brand;
use App\Model\PriceCatalog;
use Sky4\Model as Model;
use App\Classes\Pagination as Pagination;

class Metadata extends \Sky4\Metadata {

	protected $header = '';
    protected $hotlead = '';

	public function setLink($url, $relation = null, $type = null, $media = null, $attrs = []) {
		$this->links[$relation] = [
			'relation' => $relation,
			'type' => $type,
			'url' => $url,
			'media' => $media,
			'attrs' => $attrs
		];

		return $this;
	}

	public function setNew(Model $object, $filters = [], Pagination $pagination = null) {
		if ($object instanceof PriceCatalog) {
			$title = $object->getDefaultTitle($filters);
			$keywords = $object->getDefaultKeywords($filters);
			$description = $object->getDefaultDescription($filters);
		} else {
			$title = $object->val('metadata_title') ? $object->val('metadata_title') : '';
			$keywords = $object->val('metadata_key_words') ? $object->val('metadata_key_words') : '';
			$description = $object->val('metadata_description') ? $object->val('metadata_description') : '';
		}

		$title = self::replaceLocationTemplates($title);
		$keywords = self::replaceLocationTemplates($keywords);
		$description = self::replaceLocationTemplates($description);

		if ($pagination !== null) {
			if ($pagination->getTotalPages() != $pagination->getPage()) {
				$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() + 1])), 'next');
			}

			if ($pagination->getPage() !== 1) {
				$title .= ' - Страница ' . $pagination->getPage();
				$description = trim($description) . ' - Страница ' . $pagination->getPage();
				$keywords = trim($keywords) . ' - Страница ' . $pagination->getPage();

				if ($pagination->getPage() === 2) {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => null])), 'prev');
				} else {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() - 1])), 'prev');
				}
			}
		}

		$this->setTitle($title);
		$this->setMetatag('description', $description);
		$this->setMetatag('keywords', $keywords);

		return $this;
	}

	public function set(Model $object, $title, $keywords, $description, $withLocation = null, Pagination $pagination = null, $filters = [], $no_addence = null) {
		if ($object instanceof PriceCatalog) {
			if (isset($filters['mode']) && $filters['mode'] === 'price') {
				
			}
		} else {
			$title = $object->val('metadata_title') ? $object->val('metadata_title') : $title;
			$keywords = $object->val('metadata_key_words') ? $object->val('metadata_key_words') : $keywords;
			$description = $object->val('metadata_description') ? $object->val('metadata_description') : $description;
		}

		if ($withLocation) {
			$title = $object->val('metadata_title') ? $object->val('metadata_title') : self::replaceLocationTemplates($title);//главный туть обрабатывается #seo
			$keywords = $object->val('metadata_key_words') ? $object->val('metadata_key_words') : self::replaceLocationTemplates($keywords);
			$description = $object->val('metadata_description') ? $object->val('metadata_description') : self::replaceLocationTemplates($description);
		}

		if ($withLocation && !$object->val('metadata_title') && $no_addence === null) {
			$title = $title . app()->location()->currentCaseName('prepositional');
		}

		if ($pagination !== null) {
			if ($pagination->getTotalPages() != $pagination->getPage()) {
				$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() + 1])), 'next');
			}

			if ($pagination->getPage() !== 1) {
				$title .= ' - Страница ' . $pagination->getPage();
				$description = trim($description) . ' - Страница ' . $pagination->getPage();

				if ($pagination->getPage() === 2) {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => null])), 'prev');
				} else {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() - 1])), 'prev');
				}
			}
		}

		$this->setTitle($title);
		$this->setMetatag('description', $description);
		$this->setMetatag('keywords', $keywords);

		return $this;
	}

	public function setStripped(Model $object, $title, $keywords, $description, $withLocation = null, Pagination $pagination = null, $filters = [], $no_addence = null) {
		$title = $object->val('metadata_title') ? $object->val('metadata_title') : $title;
		$keywords = $object->val('metadata_key_words') ? $object->val('metadata_key_words') : $keywords;
		$description = $object->val('metadata_description') ? $object->val('metadata_description') : $description;

		$this->setTitle($title);
		$this->setStrippedMetatag('description', $description);
		$this->setStrippedMetatag('keywords', $keywords);

		return $this;
	}

	public function setTitle($title) {
		$this->title = (string) str()->replace($title, '  ', ' ');
		return $this;
	}
    
    public function setMetatag($name, $content, $attrs = []) {
		$this->metatags[$name] = [
			'attrs' => $attrs,
			'content' => $content,
			'name' => $name
		];
		return $this;
	}
    
    public function setOgMetatag($property, $content, $attrs = []) {
        $attrs['property'] = $property;
        $attrs['content'] = $content;
		$this->metatags[$property] = [
			'attrs' => $attrs,
			'content' => '',
			'name' => ''
		];
		return $this;
	}
    
	public function setFromModel(Model $object, $withLocation = null, Pagination $pagination = null, $filters = []) {
		$title = $object->val('metadata_title') ? self::replaceLocationTemplates($object->val('metadata_title')) : $object->title();
		$keywords = $object->val('metadata_key_words') ? self::replaceLocationTemplates($object->val('metadata_key_words')) : $title;
		$description = $object->val('metadata_description') ? self::replaceLocationTemplates($object->val('metadata_description')) : $title;

		if ($withLocation) {
			$title = self::replaceLocationTemplates($title);
			$keywords = self::replaceLocationTemplates($keywords);
			$description = self::replaceLocationTemplates($description);

			$title = preg_replace('~в +в~u', 'в', $title);
		}

		if ($withLocation && !$object->val('metadata_title')) {
			$title = $title . app()->location()->currentCaseName('prepositional');
		}

		if ($pagination !== null) {
			if ($pagination->getTotalPages() != $pagination->getPage()) {
				$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() + 1])), 'next');
			}

			if ($pagination->getPage() !== 1) {
				$title .= ' - Страница ' . $pagination->getPage();
				$description = trim($description) . ' - Страница ' . $pagination->getPage();
				if ($pagination->getPage() === 2) {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => null])), 'prev');
				} else {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() - 1])), 'prev');
				}
			}
		}

		$this->setTitle($title);
		$this->setMetatag('description', $description);
		$this->setMetatag('keywords', $keywords);

		return $this;
	}

	public function setDefault($title, $keywords, $description, $withLocation = null, Pagination $pagination = null, $filters = []) {
		if ($withLocation) {

			$title = self::replaceLocationTemplates($title);
			$keywords = self::replaceLocationTemplates($keywords);
			$description = self::replaceLocationTemplates($description);

			$title = preg_replace('~в +в~u', 'в', $title);
		}

		if ($pagination !== null) {
			if ($pagination->getTotalPages() != $pagination->getPage()) {
				$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() + 1])), 'next');
			}

			if ($pagination->getPage() !== 1) {
				$title .= ' - Страница ' . $pagination->getPage();
				$description = trim($description) . ' - Страница ' . $pagination->getPage();
				if ($pagination->getPage() === 2) {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => null])), 'prev');
				} else {
					$this->setLink(app()->linkFilter($pagination->getLink(), array_merge($filters, ['page' => $pagination->getPage() - 1])), 'prev');
				}
			}
		}

		$this->setTitle($title);
		$this->setMetatag('description', $description);
		$this->setMetatag('keywords', $keywords);

		return $this;
	}

	public function setIndexMeta() {
		//@todo settings
		$this->setTitle('Товары, услуги, цены ' . app()->location()->currentCaseName('prepositional') . ' - ТоварыПлюс.ру');
		$this->setMetatag('description', 'Товары Плюс (Т+)- информационно-рекламная площадка, где собрана информация о товарах и услугах фирм и предприятий ' . app()->location()->currentCaseName('genitive') . ', области и других регионов России');
		$this->setMetatag('keywords', app()->location()->currentName() . ', товары, услуги, цены, информационно-рекламная площадка ТоварыПлюс.ру, каталог фирм и предприятий');
	}

	public function getTitle() {
		return $this->title;
	}

	public function setHeader($header) {
		$this->header = $header;
		return $this;
	}

	public function getHeader() {
		return $this->header ? $this->header : $this->getTitle();
	}

	public static function replaceLocationTemplates($string) {
		$string = str()->replace($string, ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]);
		$string = preg_replace('~ {2,}~u', ' ', $string);
		$string = preg_replace('~ в в ~u', ' в ', $string);

		return $string;
	}

	public function setCanonicalUrl($local_url) {
		$this->setLink(APP_URL . $local_url, 'canonical');
		return $this;
	}

	public static function getFilterString($filters) {
		$string = [];
		foreach ($filters as $k => $v) {
			switch ($k) {
				case 'price_type': if ($v !== null) {
						$string[] = ($v === 'retail' ? 'в розницу' : 'оптом');
					}
					break;
				case 'discount': if ($v !== null) {
						$string[] = 'со скидками';
					}
					break;
				case 'brand': if ($v !== null) {
						$_brands = explode(',', $v);
						$brand = new Brand();
						$brands = $brand->reader()->objectsByIds($_brands);
						foreach ($brands as $b) {
							if ($b->exists()) {
								$string[] = $b->siteName();
							}
						}
					}
					break;
				case 'with-price': if ($v !== null) {
						$string[] = 'с ценами';
					}
					break;
				case 'prices': if ($v !== null) {
						$prices = explode(',', $v);
						if (count($prices) === 2) {
							$string[] = 'в диапазоне от ' . $prices[0] . ' до ' . $prices[1] . ' рублей';
						}
					}
					break;

				default:
					break;
			}
		}

		return $string ? ' ' . implode(', ', $string) : '';
	}

	public function noIndex($follow = false) {
		$this->setMetatag('robots', 'noindex,' . ($follow ? 'follow' : 'nofollow'));
		return $this;
	}

	public function replace($search, $replace) {
		$this->setTitle(str()->replace($this->getTitle(), $search, $replace));
		if (isset($this->metatags['keywords'])) {
			$this->setMetatag('keywords', str()->replace($this->metatags['keywords']['content'], $search, $replace));
		}
		if (isset($this->metatags['description'])) {
			$this->setMetatag('description', str()->replace($this->metatags['description']['content'], $search, $replace));
		}
		return $this;
	}

	public function renderSearchLink() {
		return html()->linkTag('search', 'application/opensearchdescription+xml', '/opensearch.xml', null, ['title' => 'Tovaryplus.ru']);
	}

	public function setStrippedMetatag($name, $content, $attrs = []) {
		$content = strip_tags($content);
		$content = htmlspecialchars($content, ENT_QUOTES);

		$this->metatags[$name] = [
			'attrs' => $attrs,
			'content' => $content,
			'name' => $name
		];
		return $this;
	}

	public function renderMetatags() {
		$result = '';
		foreach ($this->metatags as $metatag) {
			$result .= html()->metatag($metatag['name'], $metatag['content'], $metatag['attrs']);
		}
		return $result;
	}
    
    public function renderHotlead() {
        if ($this->hotlead) {
                return html()->jsFile('https://crm.hotlead.io/collector.js/'.$this->hotlead);
        }
	}

}
