<?php

namespace App\Action;

use App\Presenter\PriceItems;
use App\Model\PriceCollection;

class Collection extends \App\Action\Search {

    public function execute($mnemonick = null) {
        $filters = app()->request()->processGetParams([
            'query' => ['type' => 'string'],
            'id_catalog' => ['type' => 'int'],
            'id_city' => ['type' => 'int'],
            'mode' => ['type' => 'string'],
            'price_type' => ['type' => 'string'],
            'brand' => ['type' => 'string'],
            'with-price' => ['type' => 'string'],
            'prices' => ['type' => 'string'],
            'display_mode' => ['type' => 'string'],
            'sorting' => ['type' => 'string'],
        ]);

        $name = str_replace('.htm', '', $mnemonick);

        $_SESSION['search_settings']['mode'] = 'price';
        $_SESSION['search_settings']['city'] = (int) app()->location()->currentId() === 76 ? 'index' : app()->location()->currentId();
        $_SESSION['search_settings']['action'] = app()->link('/collection/' . $name . '.htm');

        $url = '/collection/' . $mnemonick;

        $price_collection = new PriceCollection();
        $price_collection->reader()
                ->setWhere(['AND', 'name_translit = :name_translit'], [':name_translit' => $name])
                ->objectByConds();

        if (!$price_collection->exists()) {
            throw new \Sky4\Exception();
        }
        $filters['query'] = $price_collection->val('key_1');
        $base_query = trim($filters['query']);
        $this->setQuery($base_query, true);

        $presenter = new \App\Presenter\Search();
        $presenter = $presenter->find($this->query, $filters);

        $items = $presenter->getItems();
        $has_results = count($items) > 0;
        
        if (!$has_results) {
            $filters['query'] = $price_collection->val('key_2');
            $base_query = trim($filters['query']);
            $this->setQuery($base_query, true);

            $presenter = new \App\Presenter\Search();
            $presenter = $presenter->find($this->query, $filters);

            $items = $presenter->getItems();
            $has_results = count($items) > 0;
        }

        $this->view()->set('pagination', $presenter->pagination()->render());

        //установка текста страницы и метатегов
        $this->text()->getByLink($has_results ? '/search' : 'bad_search_price');
        $this->text()->setVals([
            'text' => app()->metadata()->replaceLocationTemplates(str()->replace($this->text()->val('text'), '%query', $base_query))
        ]);
        app()->metadata()
                ->setFromModel($this->text())
                ->noIndex()
                ->replace('%query', $base_query)
                ->replace('%what', 'товаров и услуг ');

        //настройка баннеров и рекламных текстов
        /* app()->adv()
          ->reset()
          ->addKeyword($this->query); */
        if (isset($filters['id_catalog']) && $filters['id_catalog']) {
            app()->adv()
                    ->setIdSubGroup($filters['id_catalog']);
        } else {
            if (true) {
                foreach ($this->price_subgroup_matrix as $id_parent => $childs) {
                    foreach ($childs['items'] as $val) {
                        app()->adv()
                                ->setIdSubGroup($val['id_subgroup'])
                                ->setAdvertRestrictions($val['advert_restrictions'])
                                ->setAdvertAgeRestrictions($val['agelimit']);
                    }
                }
            } else {
                foreach ($this->price_catalogs as $pc) {
                    if ($pc->val('node_level') == 2) {
                        
                    }
                }
            }
        }

        app()->tabs()
                ->setTabs([
                    ['link' => app()->linkFilter(app()->link($url), $filters, ['mode' => false]), 'label' => 'Товары'],
                    ['link' => app()->linkFilter(app()->link($url), $filters, ['mode' => 'firm']), 'label' => 'Компании'],
                    ['link' => app()->linkFilter(app()->link($url), $filters, ['mode' => 'map']), 'label' => 'На карте', 'nofollow' => true]
                ])
                ->setActiveTab($filters['mode'] === 'map' ? 2 : (($filters['mode'] === 'firm') ? 1 : 0))
                ->setActiveSortOption($filters['sorting'])
                ->setFilters($filters)
                ->setTabsNumericValues([$presenter->pagination()->getTotalRecordsParam('prices'), $presenter->pagination()->getTotalRecordsParam('firms')])
                ->setDisplayMode($filters['mode'] !== 'firm' && $filters['mode'] !== 'map')
                ->setSortOptions(PriceItems::getSortingOptions());

        if ($filters['mode'] === 'map') {
            app()->setUseMap(true);
        }

        $this->view()
                ->set('tabs', app()->tabs()->render());

        app()->frontController()->layout()->setTemplate('catalog_unfixed_sidebar_footer');

        $this->view()
                ->set('bread_crumbs', app()->breadCrumbs()->render())
                ->set('title', $price_collection->name())
                ->set('filters', $filters)
                ->set('has_results', $has_results)
                ->set('query', $base_query)
                ->set('text', $this->text()->val('text'))
                //
                ->set('price_catalogs', $this->renderPriceCatalogs())
                ->set('items', $presenter->renderItems())
                ->setTemplate('prices_new', 'search')
                ->save();
    }

    public function setQuery($query, $use_synonims = false) {
        $query = str()->toLower(trim(self::clearQuery($query)));

        $we = new \App\Model\WordException();
        $exceptions = $we->reader()->rowsWithKey('name');
        $words = explode(' ', $query);
        $result_words = [];
        $_query = $query;
        foreach ($words as $word) {
            $word = trim($word);
            if (!isset($exceptions[$word])) {
                $result_words[] = $word;
            }
        }

        $query = implode(' ', $result_words);
        $result_words[] = $_query;

        if ($use_synonims) {
            //синонимы
            $replace = [];
            $syn = new \App\Model\Synonym();
            foreach ($result_words as $k => $s) {
                $synonims = $syn->reader()
                        ->setWhere(['AND', "`search` = :search"], [':search' => $s])
                        ->rows();

                foreach ($synonims as $kk => $r) {
                    $replace[$k][$kk] = $r['replace'];
                }
            }

            $words_with_synonims = [];
            if ($replace) {
                foreach ($replace as $k => $synonims) {
                    foreach ($synonims as $kk => $rep) {
                        $words_with_synonims[] = '(' . str()->replace($query, $result_words[$k], $replace[$k][$kk]) . ')';
                    }
                }
            }

            $words_with_synonims = array_unique($words_with_synonims);
            if ($words_with_synonims) {
                $query = '(' . $query . ')|' . implode('|', array_reverse($words_with_synonims));
            }
        }

        $this->query = $query;

        return $this->query;
    }

}
