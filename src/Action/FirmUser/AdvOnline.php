<?php

namespace App\Action\FirmUser;

use App\Model\Banner;
use App\Action\FirmUser;
use App\Presenter\FirmUserStatistics;
use function app;

class AdvOnline extends FirmUser {

    public function execute($html_mode = null, $filters = null) {
        $filters = $this->getFilters();
        $this->text()->getByLink('firm-user/adv-online');
        app()->metadata()->setFromModel($this->text());
        if (!$this->text()->exists()) {
            app()->metadata()->setTitle('Личный кабинет - статистика баннеров 727373');
        }

        $url = '/adv-online/';
        $base_url = self::link($url);
        if ($filters === null) {
            $filters = self::initFilters(app()->request()->processGetParams([
                                't_start' => ['type' => 'int'],
                                't_end' => ['type' => 'int'],
                                'group' => ['type' => 'string'],
                                'page' => ['type' => 'int'],
                                'id' => ['type' => 'int']
            ]));
        }

        list($dates_block, $visible) = self::getDatesBlock($url, $filters);

        if (!isset($filters['id']) || $filters['id'] === null) {
            $_banners = new \App\Model\Banner();
            $banners = $_banners->reader()
                    ->setWhere(['AND', '`site` = :site', '`id_firm` = :id_firm'],[':site' => '727373', ':id_firm' => app()->firmUser()->id_firm()])
                    ->objects();

            $count_banners = count($banners);

            if ($count_banners > 0) {
                $presenter = new FirmUserStatistics();
                $presenter->setLimit($this->isHtmlMode() ? 99999 : 20);
                $presenter->findBanners727373($filters);

                app()->metadata()
                        ->setJsFile('https://www.google.com/jsapi')
                        ->setJs('google.load("visualization", "1", {packages: ["corechart", "line"]});');

                $tabs = [
                    ['link' => app()->linkFilter($base_url, $filters, ['mode' => false]), 'label' => 'Статистика']
                ];

                app()->tabs()
                        ->setActiveTab(0)
                        ->setLink('/firm-user/adv-online/')
                        ->setTabs($tabs)
                        ->setFilters($filters)
                        ->setActiveGroupOption($filters['group'])
                        ->setGroupOptions([
                            'months' => ['name' => 'по месяцам'],
                            'weeks' => ['name' => 'по неделям'],
                ]);

                if ($this->isHtmlMode()) {
                    return $presenter->renderItems();
                }

                $this->view()
                        ->set('dates_block', $dates_block)
                        ->set('items', $presenter->renderItems())
                        ->set('pagination', $presenter->pagination()->render(true))
                        ->set('tabs', app()->tabs()->render(null, true));
            }

            $this->view()
                    ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                    ->set('has_banners', $count_banners > 0)
                    ->setTemplate('banner_index')
                    ->save();
        } else {
            if ($filters['id'] === 0 && $filters['html_mode']) {
                $_banners = new \App\Model\Banner();
                $all = $_banners->reader()
                    ->setWhere(['AND', '`site` = :site', '`id_firm` = :id_firm'],[':site' => '727373', ':id_firm' => app()->firmUser()->id_firm()])
                    ->objects();
                $result = '';

                foreach ($all as $banner) {
                    $presenter = new FirmUserStatistics();
                    $presenter->setLimit(999);
                    $presenter->findBanner727373Clicks($banner, $filters);
                    $result .= $presenter->renderItems();
                }
                return $result;
            } else {
                $banner = new Banner($filters['id']);

                app()->metadata()->setTitle('Личный кабинет - статистика баннеров - баннер #' . $banner->id());

                app()->breadCrumbs()
                        ->setElem('Статистика баннеров', '/firm-user/adv-online/')
                        ->setElem('Баннер #' . $banner->id());

                $presenter = new FirmUserStatistics();
                $presenter->setLimit(20);
                $presenter->findBanner727373Clicks($banner, $filters);

                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                        ->set('ban', $banner)
                        ->set('items', $presenter->renderItems())
                        ->set('pagination', $presenter->pagination()->render(true))
                        ->set('dates_block', $dates_block)
                        //->set('has_banners', $count_banners > 0)
                        ->setTemplate('banner_727373_clicks')
                        ->save();
            }
        }
    }

}
