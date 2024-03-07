<?php

namespace App\Classes;

use App\Model\AdvertModule;
use App\Model\AdvertModuleFirmType;
use App\Model\AdvertModuleGroup;
use App\Model\AdvertModuleRegion;
use App\Model\AdvertRestrictions;
use App\Model\Banner;
use App\Model\BannerGroup;
use App\Model\BannerRegion;
use App\Model\BannerCatalog;
use App\Model\Firm;
use App\Model\PriceCatalog;
use App\Classes\App as CApp;
use Sky4\Helper\DeprecatedDateTime as CDateTime;
use Sky4\Model\Utils as Utils;
use Sky4\View as CView;
use Foolz\SphinxQL\SphinxQL;
use App\Model\File as File;
use const APP_DIR_PATH;

class Adv {

    protected $advert_restrictions = [];
    protected $advert_age_restrictions = [];
    //
    protected $banners = null;
    protected $prior_banners = null;
    protected $advert_modules = null;
    protected $id_groups = [];
    protected $id_subgroups = [];
    protected $id_catalogs = [];
    protected $id_firm_types = [];
    protected $keywords = [];
    //
    protected $bottom_banners = [];
    protected $left_banners = [];
    protected $middle_banners = [];
    protected $rubric_banners = [];
    protected $header_banners = [];
    protected $header_banners_slider = [];
    protected $normal_banners = [];
    protected $top_banners = [];
    protected $text_banners = [];
    protected $firm_advert_modules = [];
    protected $used_middle_banner_ids = [];

    public function addKeyword($keyword) {
        if (!in_array($keyword, $this->keywords)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    public function setIdGroup($id_group) {
        if (!in_array((int) $id_group, $this->id_groups)) {
            $this->id_groups[] = (int) $id_group;
        }
        return $this;
    }

    public function setIdSubGroup($id_subgroup) {
        if (!in_array((int) $id_subgroup, $this->id_subgroups)) {
            $this->id_subgroups[] = (int) $id_subgroup;
        }
        return $this;
    }

    public function setIdFirmType($id_firm_type) {
        if (!in_array((int) $id_firm_type, $this->id_firm_types)) {
            $this->id_firm_types[] = (int) $id_firm_type;
        }
        return $this;
    }

    public function setIdCatalog($id_catalog, $prior = false) {
        if (!in_array((int) $id_catalog, array_keys($this->id_catalogs))) {

            $this->id_catalogs[(int) $id_catalog] = $prior;
        }

        return $this;
    }

    private function getBannerIdsByContext() {
        $banner_ids = [];
        $sphinx = SphinxQL::create(app()->getSphinxConnection());

        $query_words = array_diff($this->keywords, ['']);
        $count = count($query_words);

        foreach ($query_words as $key => $word) {
            $query_words[$key] = preg_replace('~[^a-zA-Zа-яА-Я0-9- ]~u', '', $query_words[$key]);
            $query_words[$key] = preg_replace('~ {1,}~u', ' ', $query_words[$key]);
        }

        $query = $query_words ? preg_replace('~\|{2,}~', '|', str_replace(' ', '|', implode('|', $query_words))) : null;
        $query = $query != null ? '(' . preg_replace('~\|~', ')|(', $query) . ')' : $query;
        $query = $query != null ? str_replace('/', '', $query) : $query;
        $query = $query != null ? preg_replace('~\|\([^)]\)~u', '', $query) : $query;

        $sphinx_results = [];
        if ($query !== null) {
            while ($count > 0) {
                $sphinx_results = $sphinx->select('id', 'site', SphinxQL::expr('WEIGHT() as weight'))
                        ->from(SPHINX_BANNER_INDEX)
                        ->orderBy('weight', 'DESC')
                        ->match('keywords_string', SphinxQL::expr($query))
                        ->option('ranker', 'matchany')
                        ->limit(0, 500)
                        ->execute();

                if ($sphinx_results) {
                    break;
                }

                $count--;
                unset($query_words[$count]);
            }

            foreach ($sphinx_results as $row) {
                if ($row['site'] != '727373') {
                    $banner_ids[] = $row['id'];
                }
            }
        }

        return $banner_ids;
    }

    private function getBannerIdsBySubGroup() {
        $result = [];
        $banner_group = new BannerGroup();
        $banner_region = new BannerRegion();
        $banner_ids_by_sub_group = [];

        $bg_conds = Utils::prepareWhereCondsFromArray($this->id_subgroups, 'id_subgroup');
        if ($bg_conds['params']) {
            $banner_ids_by_sub_group = array_keys($banner_group->reader()
                            ->setSelect(['id_banner'])
                            ->setWhere($bg_conds['where'], $bg_conds['params'])
                            ->rowsWithKey('id_banner'));
        }

        if ($banner_ids_by_sub_group) {
            $sub_group_conds = Utils::prepareWhereCondsFromArray($banner_ids_by_sub_group, 'id_banner');
            $result = array_keys($banner_region->reader()
                            ->setSelect(['id_banner'])
                            ->setWhere(['AND', 'id_region = :id_region', $sub_group_conds['where']], array_merge([':id_region' => app()->location()->region()->id()], $sub_group_conds['params']))
                            ->rowsWithKey('id_banner'));
        }

        return $result;
    }

    private function getBannerIdsByCatalog() {
        $banner_catalog = new BannerCatalog();
        $banner_region = new BannerRegion();

        $_catalog_ids = [];
        $_prior_catalog_ids = [];
        foreach ($this->id_catalogs as $_catalog_id => $prior) {
            if ($prior) {
                $_prior_catalog_ids [] = $_catalog_id;
            } else {
                $_catalog_ids [] = $_catalog_id;
            }

            if (in_array($_catalog_id, $_prior_catalog_ids) && in_array($_catalog_id, $_catalog_ids)) {
                unset($_catalog_ids[array_search($_catalog_id, $_catalog_ids)]);
            }
        }


        $banner_ids_by_catalog = [];
        if ($_catalog_ids) {
            $bc_conds = Utils::prepareWhereCondsFromArray($_catalog_ids, 'id_catalog');
            if ($bc_conds['params']) {
                $banner_ids_by_catalog = array_keys($banner_catalog->reader()
                                ->setSelect(['id_banner'])
                                ->setWhere($bc_conds['where'], $bc_conds['params'])
                                ->rowsWithKey('id_banner'));
            }
        }

        $result = [];
        $result['standart'] = [];
        $result['prior'] = [];

        if ($banner_ids_by_catalog) {
            $bc_conds = Utils::prepareWhereCondsFromArray($banner_ids_by_catalog, 'id_banner');
            $result['standart'] = array_keys($banner_region->reader()
                            ->setSelect(['id_banner'])
                            ->setWhere(['AND', 'id_region = :id_region', $bc_conds['where']], array_merge([':id_region' => app()->location()->region()->id()], $bc_conds['params']))
                            ->rowsWithKey('id_banner'));
        }

        $banner_ids_by_prior_catalog = [];
        if ($_prior_catalog_ids) {
            $bcp_conds = Utils::prepareWhereCondsFromArray($_prior_catalog_ids, 'id_catalog');
            if ($bcp_conds['params']) {
                $banner_ids_by_prior_catalog = array_keys($banner_catalog->reader()
                                ->setSelect(['id_banner'])
                                ->setWhere($bcp_conds['where'], $bcp_conds['params'])
                                ->rowsWithKey('id_banner'));
            }
        }

        if ($banner_ids_by_prior_catalog) {
            $bcp_conds = Utils::prepareWhereCondsFromArray($banner_ids_by_prior_catalog, 'id_banner');
            $result['prior'] = array_keys($banner_region->reader()
                            ->setSelect(['id_banner'])
                            ->setWhere(['AND', 'id_region = :id_region', $bcp_conds['where']], array_merge([':id_region' => app()->location()->region()->id()], $bcp_conds['params']))
                            ->rowsWithKey('id_banner'));
        }

        return $result;
    }

    private function getAdvertModuleIdsBySubGroup() {
        $result = [];
        $advert_module_group = new AdvertModuleGroup();
        $advert_module_region = new AdvertModuleRegion();
        $advert_module_ids_by_sub_group = [];

        $bg_conds = Utils::prepareWhereCondsFromArray($this->id_subgroups, 'id_subgroup');
        if ($bg_conds['params']) {
            $advert_module_ids_by_sub_group = array_keys($advert_module_group->reader()
                            ->setSelect(['id_advert_module'])
                            ->setWhere($bg_conds['where'], $bg_conds['params'])
                            ->rowsWithKey('id_advert_module'));
        }

        if ($advert_module_ids_by_sub_group) {
            $sub_group_conds = Utils::prepareWhereCondsFromArray($advert_module_ids_by_sub_group, 'id_advert_module');
            $result = array_keys($advert_module_region->reader()
                            ->setSelect(['id_advert_module'])
                            ->setWhere(['AND', 'id_region = :id_region', $sub_group_conds['where']], array_merge([':id_region' => app()->location()->region()->id()], $sub_group_conds['params']))
                            ->rowsWithKey('id_advert_module'));
        }

        return $result;
    }

    private function getBannersByIds($banner_ids = []) {
        $result_set = [];
        if ($banner_ids) {
            $banner = new Banner();
            $banner_conds = Utils::prepareWhereCondsFromArray($banner_ids);

            $where = [
                'AND',
                'flag_is_active = :flag_is_active',
                'timestamp_beginning < :now',
                'timestamp_ending > :now',
                'site <> :site',
                ['OR', '`region_ids` LIKE :like', '`region_ids` = :region_empty'],
                $banner_conds['where']
            ];

            $params = array_merge([':flag_is_active' => 1, ':site' => '727373', ':now' => CDateTime::now(), ':like' => '%' . app()->location()->getRegionId() . '%', ':region_empty' => ''], $banner_conds['params']);

            $result_set = $banner->reader()
                    ->setWhere($where, $params)
                    ->setOrderBy('RAND()')
                    ->objects();
        }

        return $result_set;
    }

    private function getAdvertModuleIdsByFirmTypes() {
        $result = [];
        $advert_module_firm_type = new AdvertModuleFirmType();
        $advert_module_region = new AdvertModuleRegion();
        $advert_module_ids_by_firm_type = [];

        $amft_conds = Utils::prepareWhereCondsFromArray($this->id_firm_types, 'id_firm_type');
        if ($amft_conds['params']) {
            $advert_module_ids_by_firm_type = array_keys($advert_module_firm_type->reader()
                            ->setSelect(['id_advert_module'])
                            ->setWhere($amft_conds['where'], $amft_conds['params'])
                            ->rowsWithKey('id_advert_module'));
        }

        if ($advert_module_ids_by_firm_type) {
            $firm_type_conds = Utils::prepareWhereCondsFromArray($advert_module_ids_by_firm_type, 'id_advert_module');
            $result = array_keys($advert_module_region->reader()
                            ->setSelect(['id_advert_module'])
                            ->setWhere(['AND', 'id_region = :id_region', $firm_type_conds['where']], array_merge([':id_region' => app()->location()->region()->id()], $firm_type_conds['params']))
                            ->rowsWithKey('id_advert_module'));
        }

        return $result;
    }

    public function getAdvertModulesByFirm(Firm $firm) {
        $result_set = [];

        if ($firm->exists() && $firm->priority() < 20) {
            $firm_types = $firm->getTypes();

            $amft = new AdvertModuleFirmType();
            $advert_module_ids = $amft->getAdvertModuleIdsByFirmTypes(array_keys($firm_types));
            if (count($advert_module_ids) > 0) {
                $advert_module_conds = Utils::prepareWhereCondsFromArray($advert_module_ids);

                $where = [
                    'AND',
                    'flag_is_active = :flag_is_active',
                    'timestamp_beginning < :now',
                    'timestamp_ending > :now',
                    ['OR', '`region_ids` LIKE :like', '`region_ids` = :region_empty'],
                    $advert_module_conds['where']
                ];

                $params = array_merge([':flag_is_active' => 1, ':now' => CDateTime::now(), ':like' => '%' . app()->location()->getRegionId() . '%', ':region_empty' => ''], $advert_module_conds['params']);

                $advert_module = new AdvertModule();
                $result_set = $advert_module->reader()
                        ->setWhere($where, $params)
                        ->setOrderBy('RAND()')
                        ->objects();
            }
        }

        return $result_set;
    }

    private function getAdvertModulesByIds($advert_module_ids = []) {
        $result_set = [];
        if ($advert_module_ids) {
            $advert_module = new AdvertModule();
            $advert_module_conds = Utils::prepareWhereCondsFromArray($advert_module_ids);

            $where = [
                'AND',
                'flag_is_active = :flag_is_active',
                'timestamp_beginning < :now',
                'timestamp_ending > :now',
                ['OR', '`region_ids` LIKE :like', '`region_ids` = :region_empty'],
                $advert_module_conds['where']
            ];

            $params = array_merge([':flag_is_active' => 1, ':now' => CDateTime::now(), ':like' => '%' . app()->location()->getRegionId() . '%', ':region_empty' => ''], $advert_module_conds['params']);

            $result_set = $advert_module->reader()
                    ->setWhere($where, $params)
                    ->setOrderBy('RAND()')
                    ->objects();
        }

        return $result_set;
    }

    private function getBannersCommon() {
        $banner = new Banner();

        $where = [
            'AND',
            'flag_is_active = :flag_is_active',
            'flag_is_everywhere = :flag_is_everywhere',
            'timestamp_beginning < :now',
            'timestamp_ending > :now',
            'site <> :site'
        ];

        $params = [
            ':flag_is_active' => 1,
            ':flag_is_everywhere' => 1,
            ':site' => '727373',
            ':now' => CDateTime::now()
        ];

        $banners = $banner->reader()
                ->setWhere($where, $params)
                ->setOrderBy('RAND()')
                ->objects();
        $result_set = [];
        foreach ($banners as $_banner) {
            if ($_banner->isForCurrentRegion()) {
                $result_set[] = $_banner;
            }
        }

        return $result_set;
    }

    private function setBanners() {
        $this->setStandartBanners();
        $this->setPriorBanners();

        $this->banners = array_udiff($this->banners, $this->prior_banners, function ($banner, $prior_banner) {
            return $banner->id() === $prior_banner->id() ? 0 : -1;
        });

        return $this;
    }

    private function setStandartBanners() {
        $banner_ids_by_catalog = $this->getBannerIdsByCatalog();
        $banner_ids_by_sub_group = $this->getBannerIdsBySubGroup();
        $banner_ids_by_context = $this->getBannerIdsByContext();
        /* var_dump($banner_ids_by_catalog);
          var_dump($banner_ids_by_sub_group);
          var_dump($banner_ids_by_context); */

        $intersected_banners = [];

        $intersected_banners = array_merge($intersected_banners, array_intersect($banner_ids_by_context, $banner_ids_by_sub_group));
        $intersected_banners = array_merge($intersected_banners, array_intersect($banner_ids_by_context, $banner_ids_by_catalog['standart']));
        $intersected_banners = array_merge($intersected_banners, array_intersect($banner_ids_by_sub_group, $banner_ids_by_catalog['standart']));

        $banner_ids_by_context = array_diff($banner_ids_by_context, $intersected_banners);
        $banner_ids_by_sub_group = array_diff($banner_ids_by_sub_group, $intersected_banners);
        $banner_ids_by_catalog['standart'] = array_diff($banner_ids_by_catalog['standart'], $intersected_banners);

        $intersected_banners = array_unique($intersected_banners);


        $this->banners = [];
        $this->banners = array_merge($this->banners, $this->getBannersByIds($banner_ids_by_catalog['standart']));
        $this->banners = array_merge($this->banners, $this->getBannersByIds($intersected_banners));
        $this->banners = array_merge($this->banners, $this->getBannersByIds($banner_ids_by_sub_group));

        $this->banners = array_merge($this->banners, $this->getBannersByIds($banner_ids_by_context));


        $this->banners = array_merge($this->banners, $this->getBannersCommon());

        return $this;
    }

    private function setPriorBanners() {
        $banner_ids_by_catalog = $this->getBannerIdsByCatalog();

        //$banner_ids_by_sub_group = $this->getBannerIdsBySubGroup();
        //$banner_ids_by_context = $this->getBannerIdsByContext();
        $intersected_banners = [];

        $intersected_banners = array_diff($intersected_banners, $banner_ids_by_catalog['prior']);
        $intersected_banners = array_unique($intersected_banners);
        $banner_ids_by_catalog['prior'] = array_diff($banner_ids_by_catalog['prior'], $intersected_banners);

        $this->prior_banners = [];
        $this->prior_banners = array_merge($this->prior_banners, $this->getBannersByIds($intersected_banners));
        $this->prior_banners = array_merge($this->prior_banners, $this->getBannersByIds($banner_ids_by_catalog['prior']));

        return $this;
    }

    private function setAdvertModules() {
        $advert_module_ids_by_firm_type = $this->getAdvertModuleIdsByFirmTypes();
        $advert_module_ids_by_sub_group = $this->getAdvertModuleIdsBySubGroup();
        $intersected_advert_modules = [];

        foreach ($advert_module_ids_by_firm_type as $am_id) {
            if (in_array($am_id, $advert_module_ids_by_sub_group)) {
                $intersected_advert_modules[] = $am_id;
                unset($advert_module_ids_by_sub_group[array_search($am_id, $advert_module_ids_by_sub_group)]);
                unset($advert_module_ids_by_firm_type[array_search($am_id, $advert_module_ids_by_firm_type)]);
            }
        }

        $this->advert_modules = [];
        $this->advert_modules = array_merge($this->advert_modules, $this->getAdvertModulesByIds($intersected_advert_modules));
        $this->advert_modules = array_merge($this->advert_modules, $this->getAdvertModulesByIds($advert_module_ids_by_sub_group));
        $this->advert_modules = array_merge($this->advert_modules, $this->getAdvertModulesByIds($advert_module_ids_by_firm_type));

        return $this;
    }

    public function getBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        return $this->banners;
    }

    public function getAdvertModules() {
        if ($this->advert_modules === null) {
            $this->setAdvertModules();
        }

        return $this->advert_modules;
    }

    public function getRubricsBanners() {
        $banners = [];

        $b = new Banner();
        $banner_ids = array_keys($b->reader()->setWhere(['OR', 'type = :type1', 'type = :type2'], [':type1' => 'rubrics_big_banner', ':type2' => 'rubrics_small_banner'])->rowsWithKey('id'));
        $banners = $this->getBannersByIds($banner_ids);

        $rubric_banners_big = [];
        $rubric_banners_small = [];

        foreach ($banners as $banner) {
            switch ($banner->val('type')) {
                case 'rubrics_big_banner' : $rubric_banners_big[] = $banner;
                    break;
                case 'rubrics_small_banner' : $rubric_banners_small[] = $banner;
                    break;
            }
        }

        $this->fixShows($banners);

        $banners = [];

        if ($rubric_banners_big) {
            //shuffle($rubric_banners_big);
            $banners = array_merge($banners, $rubric_banners_big);

            if ($rubric_banners_small) {
                //shuffle($rubric_banners_small);
                $i = 0;
                foreach ($rubric_banners_small as $ban) {
                    $i++;
                    $ban->_temp_type = 'rubrics_small_banner';
                    $ban->setVal('type', 'rubrics_big_banner');
                    $banners[] = $ban;
                    if ($i === 2)
                        break;
                }
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->rubric_banners[$ban->val('type')])) {
                $this->rubric_banners[$ban->val('type')] = [];
            }
            $this->rubric_banners[$ban->val('type')][$ban->id()] = $ban;
        }

        return $this->rubric_banners;
    }

    public function getTopBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $prior_default = [];
        $prior_context = [];
        $prior_normal = [];
        $prior_normal_2 = [];
        $prior_wide = [];

        foreach ($this->prior_banners as $banner) {
            switch ($banner->val('type')) {
                /* case 'wide_banner' : $prior_wide[] = $banner;
                  break;
                  case 'default_banner' : $prior_default[] = $banner;
                  break;
                 */
                case 'normal_banner' :
                    if (!isset($this->normal_banners[$banner->val('type')][$banner->id()])) {
                        $prior_normal[] = $banner;
                    }
                    break;
                case '' :
                    $prior_normal_2[] = $banner;
                    break;
                default:
                    break;
            }
        }

        $default = [];
        $context = [];
        $normal = [];
        $normal_2 = [];
        $wide = [];

        foreach ($this->banners as $banner) {
            switch ($banner->val('type')) {
                /* case 'wide_banner' : $wide[] = $banner;
                  break;
                  case 'default_banner' : $default[] = $banner;
                  break;
                 */
                case 'normal_banner' :
                    if (!isset($this->normal_banners[$banner->val('type')][$banner->id()])) {
                        $normal[] = $banner;
                    }
                    break;
                case '' :
                    $normal_2[] = $banner;
                    break;

                default:
                    break;
            }
        }

        $banners = [];
        $normal_banners_counter = 2;
        $wide_banners_counter = 1;
        $context_banners_counter = 3;

        if (array_merge($prior_normal, $normal)) {
            foreach (array_merge($prior_normal, $normal) as $ban) {
                if ($normal_banners_counter === 0)
                    break;
                $banners[] = $ban;
                $normal_banners_counter--;
            }
        } elseif (array_merge($prior_normal_2, $normal_2)) {
            foreach (array_merge($prior_normal_2, $normal_2) as $ban) {
                if ($normal_banners_counter === 0)
                    break;
                $banners[] = $ban;
                $normal_banners_counter--;
            }
        } else {
            if (array_merge($prior_wide, $wide)) {
                $banners[] = current(array_merge($prior_wide, $wide));
                $wide_banners_counter--;
            } else {
                if (array_merge($prior_context, $context)) {
                    foreach (array_merge($prior_context, $context) as $ban) {
                        if ($context_banners_counter === 0)
                            break;
                        $banners[] = $ban;
                        $context_banners_counter--;
                    }
                }
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->top_banners[$ban->val('type')])) {
                $this->top_banners[$ban->val('type')] = [];
            }
            $this->top_banners[$ban->val('type')][$ban->id()] = $ban;
        }

        return $this->top_banners;
    }

    public function getFirmAdvertModules() {
        if ($this->advert_modules === null) {
            $this->setAdvertModules();
        }

        $default = [];
        $normal = [];
        $wide = [];
        $types = [];
        shuffle($types);

        foreach ($this->advert_modules as $advert_module) {
            if (!in_array($advert_module->val('type'), $types)) {
                $types[] = $advert_module->val('type');
            }

            switch ($advert_module->val('type')) {
                case 'wide_advert_module' :
                    $wide[] = $advert_module;
                    break;
                case 'default_advert_module' :
                    $default[] = $advert_module;
                    break;
                /* case '' : $normal[] = $advert_module;
                  break; */
            }
        }
        $random_type = current($types);

        $advert_modules = [];

        if ($wide && $default) {
            switch ($random_type) {
                case 'wide_advert_module':
                    $advert_modules[] = current($wide);
                    break;
                case 'default_advert_module':
                    $i = 0;
                    foreach ($default as $am) {
                        $i++;
                        $advert_modules[] = $am;
                        if ($i === 3)
                            break;
                    }
                    break;
            }
        } else if ($wide) {
            $advert_modules[] = current($wide);
        } else {
            $i = 0;
            foreach ($default as $am) {
                $i++;
                $advert_modules[] = $am;
                if ($i === 3)
                    break;
            }
        }

        foreach ($advert_modules as $am) {
            if (!isset($this->firm_advert_modules[$am->val('type')])) {
                $this->firm_advert_modules[$am->val('type')] = [];
            }
            $this->firm_advert_modules[$am->val('type')][$am->id()] = $am;
        }
        return $this->firm_advert_modules;
    }

    public function getMiddleBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $prior_context = [];
        foreach ($this->prior_banners as $banner) {
            if ($banner->val('type') == '' && !isset($this->top_banners[$banner->val('type')][$banner->id()]) && !isset($this->middle_banners[$banner->val('type')][$banner->id()])) {
                $prior_context[] = $banner;
            }
        }

        $context = [];
        foreach ($this->banners as $banner) {
            if ($banner->val('type') == '' && !isset($this->top_banners[$banner->val('type')][$banner->id()]) && !isset($this->middle_banners[$banner->val('type')][$banner->id()])) {
                $context[] = $banner;
            }
        }

        $banners = [];
        $middle_banners_count = 2;

        if ($prior_context) {
            shuffle($prior_context);
            foreach ($prior_context as $ban) {
                if ($middle_banners_count === 0)
                    break;
                $banners[] = $ban;
                $middle_banners_count--;
            }
        }

        if ($context) {
            shuffle($context);
            foreach ($context as $ban) {
                if ($middle_banners_count === 0)
                    break;
                $banners[] = $ban;
                $middle_banners_count--;
            }
        }
        
        $this->middle_banners = [];
        
        foreach ($banners as $ban) {
            if (!in_array($ban->id(), $this->used_middle_banner_ids)) {
                if (!isset($this->middle_banners[$ban->val('type')])) {
                    $this->middle_banners[$ban->val('type')] = [];
                }
                if (!isset($this->middle_banners[$ban->val('type')][$ban->id()])) {
                    $this->middle_banners[$ban->val('type')][$ban->id()] = $ban;
                }

                $this->used_middle_banner_ids []= $ban->id();
            }
        }

        return $this->middle_banners;
    }

    public function getBottomBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $banners = [];
        $bottom_banners_count = 1;

        foreach ($this->prior_banners as $banner) {
            if ($bottom_banners_count === 0)
                break;
            if ($banner->val('type') === 'wide_banner' && $banner->isEverywhere() && $banner->isForCurrentRegion()) {
                $banners[] = $banner;
                $bottom_banners_count--;
            }
        }


        foreach ($this->banners as $banner) {
            if ($bottom_banners_count === 0)
                break;
            if ($banner->val('type') === 'wide_banner' && $banner->isEverywhere() && $banner->isForCurrentRegion()) {
                $banners[] = $banner;
                $bottom_banners_count--;
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->bottom_banners[$ban->val('type')])) {
                $this->bottom_banners[$ban->val('type')] = [];
            }
            $this->bottom_banners[$ban->val('type')][$ban->id()] = $ban;
        }

        return $this->bottom_banners;
    }

    public function getLeftBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $prior_default = [];
        foreach ($this->prior_banners as $banner) {
            if ($banner->val('type') == 'default_banner' && !isset($this->top_banners[$banner->val('type')][$banner->id()])) {
                $prior_default[] = $banner;
            }
        }

        $default = [];
        foreach ($this->banners as $banner) {
            if ($banner->val('type') == 'default_banner' && !isset($this->top_banners[$banner->val('type')][$banner->id()])) {
                $default[] = $banner;
            }
        }

        $banners = [];
        $left_banners_count = (int) app()->config()->get('app.banners.left.count', 3);
        if ($prior_default) {
            foreach ($prior_default as $ban) {
                if ($left_banners_count === 0)
                    break;
                $banners[] = $ban;
                $left_banners_count--;
            }
        }

        if ($default) {
            foreach ($default as $ban) {
                if ($left_banners_count === 0)
                    break;
                $banners[] = $ban;
                $left_banners_count--;
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->left_banners[$ban->val('type')])) {
                $this->left_banners[$ban->val('type')] = [];
            }
            $this->left_banners[$ban->val('type')][$ban->id()] = $ban;
        }

        return $this->left_banners;
    }

    public function getTextBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $prior_context = [];
        foreach ($this->prior_banners as $banner) {
            if ($banner->val('type') == '') {
                $prior_context[] = $banner;
            }
        }

        $context = [];
        foreach ($this->banners as $banner) {
            if ($banner->val('type') == '') {
                $context[] = $banner;
            }
        }

        $banners = [];
        $text_banners_count = 3;
        if ($prior_context) {
            //shuffle($prior_context);
            foreach ($prior_context as $ban) {
                if ($text_banners_count === 0)
                    break;
                $banners[] = $ban;
                $text_banners_count--;
            }
        }

        if ($context) {
            //shuffle($context);
            foreach ($context as $ban) {
                if ($text_banners_count === 0)
                    break;
                $banners[] = $ban;
                $text_banners_count--;
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->text_banners[$ban->val('type')])) {
                $this->text_banners[$ban->val('type')] = [];
            }
            $this->text_banners[$ban->val('type')][$ban->id()] = $ban;
        }

        return $this->text_banners;
    }

    public function getHeaderBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $banners = [];
        $header_banners_count = 2;

        foreach ($this->prior_banners as $banner) {
            if ($header_banners_count === 0)
                break;
            if ($banner->val('type') === 'header_banner' && $banner->isForCurrentRegion()) {
                $banners[] = $banner;
                $header_banners_count--;
            }
        }

        foreach ($this->banners as $banner) {
            if ($header_banners_count === 0)
                break;
            if ($banner->val('type') === 'header_banner' && $banner->isForCurrentRegion()) {
                $banners[] = $banner;
                $header_banners_count--;
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->header_banners[$ban->val('type')])) {
                $this->header_banners[$ban->val('type')] = [];
            }
            $this->header_banners[$ban->val('type')][$ban->id()] = $ban;
            unset($this->banners[$ban->id()]);
        }

        return $this->header_banners;
    }

    public function getIndexBanners() {
        $banners = [];

        $b = new Banner();
        $banner_ids = array_keys($b->reader()->setWhere(['OR', 'type = :type1', 'type = :type2'], [':type1' => 'header_banner', ':type2' => 'rubrics_small_banner'])->rowsWithKey('id'));
        $banners = $this->getBannersByIds($banner_ids);

        $index_header_banners = [];
        $index_small_banners = [];

        foreach ($banners as $banner) {
            switch ($banner->val('type')) {
                case 'header_banner' :
                    $index_header_banners[] = $banner;
                    break;
                case 'rubrics_small_banner' :
                    $index_small_banners[] = $banner;
                    break;
            }
        }

        $banners = [];

        if ($index_header_banners) {
            shuffle($index_header_banners);
            $index_header_banners_count = 2;
            foreach ($index_header_banners as $ban) {
                $index_header_banners_count--;
                $ban->setVal('type', 'header_banner');
                $banners[] = $ban;
                if ($index_header_banners_count === 0)
                    break;
            }

            if ($index_small_banners) {
                shuffle($index_small_banners);
                $index_small_banners_count = 1;
                foreach ($index_small_banners as $ban) {
                    $index_small_banners_count--;
                    $ban->_temp_type = 'rubrics_small_banner';
                    $ban->setVal('type', 'header_banner');
                    $banners[] = $ban;
                    if ($index_small_banners_count === 0)
                        break;
                }
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->header_banner[$ban->val('type')])) {
                $this->header_banner[$ban->val('type')] = [];
            }
            $this->header_banner[$ban->val('type')][$ban->id()] = $ban;
        }

        return isset($this->header_banner) ? $this->header_banner : [];
    }

    public function getIndexRubricBannersSlider() {
        $banners = [];

        $b = new Banner();
        $banner_ids = array_keys($b->reader()->setWhere(['AND', 'type = :type'], [':type' => 'rubrics_big_banner'])->rowsWithKey('id'));
        $index_rubric_banners = $this->getBannersByIds($banner_ids);

        if ($index_rubric_banners) {
            shuffle($index_rubric_banners);
            $index_rubric_banners_count = 6;
            foreach ($index_rubric_banners as $ban) {
                $index_rubric_banners_count--;
                $ban->setVal('type', 'header_banner');
                $banners[] = $ban;
                if ($index_rubric_banners_count === 0)
                    break;
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->header_banners_slider['header_banner_slider'])) {
                $this->header_banners_slider['header_banner_slider'] = [];
            }
            $this->header_banners_slider['header_banner_slider'][$ban->id()] = $ban;
        }

        return $this->header_banners_slider;
    }

    public function getNormalBanners() {
        if ($this->banners === null) {
            $this->setBanners();
        }

        $banners = [];
        $normal_banners_count = 2;

        foreach ($this->prior_banners as $banner) {
            if ($normal_banners_count === 0)
                break;
            if ($banner->val('type') === 'normal_banner' && $banner->isEverywhere() && $banner->isForCurrentRegion()) {
                $banners[] = $banner;
                $normal_banners_count--;
            }
        }

        foreach ($this->banners as $banner) {
            if ($normal_banners_count === 0)
                break;
            if ($banner->val('type') === 'normal_banner' && $banner->isEverywhere() && $banner->isForCurrentRegion()) {
                $banners[] = $banner;
                $normal_banners_count--;
            }
        }

        foreach ($banners as $ban) {
            if (!isset($this->normal_banners[$ban->val('type')])) {
                $this->normal_banners[$ban->val('type')] = [];
            }
            $this->normal_banners[$ban->val('type')][$ban->id()] = $ban;
            unset($this->banners[$ban->id()]);
        }

        return $this->normal_banners;
    }

    public function renderRestrictions() {
        return app()->chunk()->setArg([$this->advert_restrictions])->render('adv.advert_restrictions');
    }

    public function renderAgeRestrictions() {
        return app()->chunk()->setArg([$this->advert_age_restrictions])->render('adv.advert_age_restrictions');
    }

    public function reset() {
        $this->id_subgroups = [];
        $this->id_groups = [];
        $this->keywords = [];
        return $this;
    }

    public function setAdvertRestrictions($restriction) {
        if ($restriction) {
            if (!in_array((string) $restriction, $this->advert_restrictions)) {
                $this->advert_restrictions[] = (string) $restriction;
            }
        }

        return $this;
    }

    public function getAdvertAgeRestrictions() {
        return $this->advert_age_restrictions;
    }

    public function setAdvertAgeRestrictions($restriction) {
        if ($restriction) {
            if (!in_array((string) $restriction, $this->advert_age_restrictions)) {
                $this->advert_age_restrictions[] = (string) $restriction;
            }
        }

        return $this;
    }

    public function renderBannerImageLink(Banner $banner, File $image, $image_style = null) {
        $view = new \CView();

        return $view
                        ->set('banner', $banner)
                        ->set('image', $image)
                        ->set('image_style', $image_style)
                        ->set('is_swf', $image->val('file_extension') === 'swf')
                        ->setTemplate('banner', 'adv')
                        ->render();
    }

    public function renderAdvertModuleImageLink(AdvertModule $advert_module, $image, $image_style = null) {
        $view = new \CView();

        return $view
                        ->set('advert_module', $advert_module)
                        ->set('image', $image)
                        ->set('image_style', $image_style)
                        ->set('is_swf', $image->val('file_extension') === 'swf')
                        ->setTemplate('advert_module', 'adv')
                        ->render();
    }

    public function renderBannerPlace($items) {
        $result = '';

        foreach ($items as $type => $banners) {
            switch ($type) {
                case 'slider_banner' : $template = 'banner_slider';
                    break;
                case 'wide_banner' : $template = 'banner_wide';
                    break;
                case 'default_banner' : $template = 'banner_default';
                    break;
                case 'header_banner' : $template = 'banner_header';
                    break;
                case 'header_banner_slider' : $template = 'banner_header_slider';
                    break;
                case 'normal_banner' : $template = 'banner_normal';
                    break;
                //case '' : $template = 'banner_text';
                default :
                case '' : $template = 'banner_normal';
                    break;
            }

            $view = new \CView();

            shuffle($banners);

            $view
                    ->setThemeDir()
                    ->setTemplate($template, 'adv')
                    ->set('items', $banners);

            $result .= $view->render();

            $this->fixShows($banners);
        }

        return $result;
    }

    public function renderBanner(Banner $banner, $reports_mode = null) {
        $result = '';

        switch ($banner->val('type')) {
            case 'slider_banner' : $template = 'banner_slider';
                break;
            case 'wide_banner' : $template = 'banner_wide';
                break;
            case 'default_banner' : $template = 'banner_default';
                break;
            case 'header_banner' : $template = 'banner_header';
                break;
            case 'normal_banner' : $template = 'banner_normal';
                break;
            case 'rubrics_big_banner' : $template = 'banner_rubrics';
                break;
            //default : $template = 'banner_text';
            default :
            case '' : $template = 'banner_normal';
                break;
        }

        $view = new \CView();

        if ($reports_mode) {
            $template = 'banner_for_report';
        }

        $result = $view
                ->setDirPath(APP_DIR_PATH . '/src/views/adv')
                ->setTemplate($template)
                ->set('items', [$banner])
                ->render();

        return $result;
    }

    public function renderAdvertModule(AdvertModule $advert_module, $reports_mode = null) {
        $result = '';

        switch ($advert_module->val('type')) {
            default : //$template = 'advert_module_text';
            //break;
            case 'wide_advert_module' : //$template = 'advert_module_wide';
            //break;
            case 'default_advert_module' : $template = 'advert_module_default';
                break;
        }

        $view = new \CView();

        if ($reports_mode) {
            $template = 'advert_module_for_report';
        }

        $result = $view
                ->setDirPath(APP_DIR_PATH . '/src/views/adv')
                ->setTemplate($template)
                ->set('items', [$advert_module])
                ->render();

        return $result;
    }

    public function renderAdvertModulePlace($items) {
        $result = '';

        foreach ($items as $type => $advert_modules) {
            switch ($type) {
                case 'wide_advert_module' : $template = 'advert_module_wide';
                    break;
                case 'default_advert_module' : $template = 'advert_module_default';
                    break;
                default : $template = 'advert_module_text';
                    break;
            }

            $view = new \CView();

            shuffle($advert_modules);

            $advert_modules = $this->setAdvertModulesAdvertRestrictions($advert_modules);

            $result .= $view
                    ->setDirPath(APP_DIR_PATH . '/src/views/adv')
                    ->setTemplate($template)
                    ->set('items', $advert_modules)
                    ->render();
        }

        return $result;
    }

    public function renderFirmAdvertModulesByFirm(Firm $firm) {
        $advert_modules = [];

        $advert_modules[] = self::getAdvertModulesByFirm($firm);

        return self::renderAdvertModulePlace($advert_modules);
    }

    public function fixShows($banners) {
        foreach ($banners as $ban) {
            app()->stat()->fixBannerShow($ban);
        }
    }

    public function setAdvertModulesAdvertRestrictions($items) {
        $advert_modules = [];
        foreach ($items as $item) {
            $subgroup_ids = explode(',', $item->val('subgroup_ids'));
            $subgroup_ids = array_filter($subgroup_ids);
            $subgroup_ids = array_unique($subgroup_ids);

            $_catalogs = [];

            if (count($subgroup_ids) > 0) {
                $subgroup_conds = Utils::prepareWhereCondsFromArray($subgroup_ids, 'id_subgroup');

                $__where = ['AND', '`node_level` = :node_level', $subgroup_conds['where']];
                $__params = [':node_level' => 2] + $subgroup_conds['params'];

                $cat = new PriceCatalog();
                $_catalogs = $cat->reader()
                        ->setWhere($__where, $__params)
                        ->objects();
            }

            foreach ($_catalogs as $_catalog) {
                $catalogs = $_catalog->adjacencyListComponent()->getPath();
                foreach ($catalogs as $cat) {
                    if ($cat->val('advert_restrictions')) {
                        $adv = new AdvertRestrictions($cat->val('advert_restrictions'));
                        $item->restrictions [] = $adv;
                    }
                }
            }

            $advert_modules [] = $item;
        }

        return $advert_modules;
    }

}
