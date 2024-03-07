<?php

namespace App\Classes;

use App\Model\PriceCatalog;
use App\Model\Yml;
use App\Model\YmlCategory;
use App\Model\YmlImage;
use App\Model\YmlOffer;
use App\Model\YmlParam;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Component\FileUploader;
use Sky4\Component\ImageProcessor\Gd;
use Sky4\Component\ImageProcessor\Imagick;
use Sky4\FileSystem\Dir;
use Sky4\Helper\DateTime;
use const SPHINX_MAX_INT;
use function app;

class YmlParser {

    private $data = null;
    private $xml_data = null;
    private $hash = '';
    private $id_firm = null;
    private $id_yml_model = null;
    private $name_format = null;
    private $flag_is_referral = 1;
    private $error_code = 0;
    private $type = '';
    private $file_uploader = null;
    private $image_processor = null;
    private $url = null;

    public function setFlagIsReferral($flag) {
        $this->flag_is_referral = (int) $flag;
        return $this;
    }

    public function setNameFormat($format = []) {
        if ($format) {
            $this->name_format = $format;
        } else {
            $this->name_format = ['name'];
        }

        return $this;
    }

    public function setXmlData($data, $id_firm, $type = '', $url = '', $id_yml_model = '') {
        $this->data = $data;
        $this->xml_data = simplexml_load_string($this->data);
        $this->hash = md5($data);
        $this->type = $type;
        $this->id_firm = (int) $id_firm;
        $this->url = (string) $url;
        $this->id_yml_model = (int) $id_yml_model;

        return $this;
    }

    public function setXmlDataFromYmlObject(Yml $yml) {
        $this->data = $yml->val('data');
        $this->xml_data = simplexml_load_string($this->data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
        $this->hash = md5($this->data);
        $this->type = $yml->val('type');
        $this->id_firm = (int) $yml->val('id_firm');
        $this->id_yml_model = (int) $yml->id();
        $offers = $this->xml_data->xpath('/yml_catalog/shop/offers/offer');
        $count = count($offers);
        $yml->update([
            'offers_count' => $count,
            'offers_count_loaded' => 0,
        ]);

        return $this;
    }

    /**
     * 
     * @return SimpleXMLElement
     */
    public function getXmlData() {
        return $this->xml_data;
    }

    public function setErrorCode($code) {
        $this->error_code = $code;
        return $this;
    }

    public function parse() {
        $yml = $this->getXmlData();
        $categories = [];
        $catalog_timestamp = strtotime($yml->attributes()['date']);

        foreach ($yml->xpath('/yml_catalog/shop') as $element) {
            foreach ($element->xpath('categories/category') as $category) {
                $cat = new YmlCategory();
                $cat->reader()
                        ->setWhere(['AND', 'id_yml_category = :id_category', 'id_firm = :id_firm'], [':id_category' => (int) $category->attributes()->id, ':id_firm' => $this->getIdFirm()])
                        ->objectByConds();
                if (!$cat->exists()) {
                    $cat->insert([
                        'id_yml_category' => (int) $category->attributes()->id,
                        'parent_node' => (int) $category->attributes()->parentId ?? 0,
                        'name' => (string) trim($category),
                        'id_firm' => $this->getIdFirm()
                    ]);
                }

                $categories[(int) $category->attributes()->id] = [
                    'id' => $cat->id(),
                    'name' => $cat->name()
                ];
            }
        }

        app()->log('Категории установлены', 1);
        $this->compareCategories();
        app()->log('Категории сравнены', 1);

        app()->db()->query()->setText('UPDATE `yml_offer` SET `status` = :status WHERE `id_firm` = :id_firm')
                ->execute([':status' => 'deleted', 'id_firm' => $this->getIdFirm()]);

        app()->log('Статусы оферов зачищены', 1);

        app()->db()->query()->setText('UPDATE `price` SET `flag_is_active` = :nil WHERE `id_firm` = :id_firm AND `source` = :source')
                ->execute([':nil' => 0, 'id_firm' => $this->getIdFirm(), ':source' => 'yml']);

//		$price_catalog_price = new \App\Model\PriceCatalogPrice();
//		$price_catalog_price->deleteAll(['AND', 'id_firm = :id_firm'], null, null, null, [':id_firm' => $this->getIdFirm()]);

        app()->log('Прайсы зачищены', 1);

        $offers = $yml->xpath('/yml_catalog/shop/offers/offer');
        $i = 0;
        $i_ended = 0;
        $insert_counter = 0;
        $update_counter = 0;
        
        $_offer = new YmlOffer();
        $_count = $_offer->reader()
                ->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => $this->getIdFirm()])
                ->count();
        
        app()->log('Найдено офферров: ' . count($offers), 1);
        app()->log('Офферров в таблице: ' . $_count, 1);
        
        foreach ($offers as $ofr) {
            $ofr_id = (int) $ofr->attributes()->id ? (int) $ofr->attributes()->id : $this->getCRC32Checksum($ofr->attributes()->id);
            $i++;
            $offer = new YmlOffer();
            $offer->reader()
                    ->setWhere(['AND', 'id_yml = :id_yml', 'id_firm = :id_firm'], [':id_yml' => $ofr_id, ':id_firm' => $this->getIdFirm()])
                    ->objectByConds();

            $timestamp_update = new DateTime();
            $timestamp_update->fromTimestamp($ofr->modified_time ? (int) $ofr->modified_time : $catalog_timestamp);

            if ($offer->exists() && $offer->val('timestamp') === $timestamp_update->format()) {
                $offer->update(['status' => '']);
                continue;
            }
            $i_ended++;

            $insert_mode = 'insert';
            if ($offer->exists()) {
                $insert_mode = 'update';
            }

            $name_format = $this->getNameFormat();
            $name = [];
            foreach ($name_format as $field) {
                if (isset($ofr->{$field})) {
                    $name[] = (string) $ofr->{$field};
                }
            }

            if ($insert_mode === 'insert') {
                $offer_vals = [
                    'id_catalog' => 0,
                    'id_firm' => $this->getIdFirm(),
                    'id_yml' => $ofr_id,
                    'id_yml_category' => isset($categories[(int) $ofr->categoryId]) ? $categories[(int) $ofr->categoryId]['id'] : 0,
                    'id_yml_file' => $this->getYmlFileId(),
                    'currency' => (string) $ofr->currencyId,
                    'country_of_origin' => (string) $ofr->country_of_origin,
                    'description' => (string) $ofr->description,
                    'images' => null,
                    'name' => implode(' ', $name),
                    'timestamp' => $timestamp_update->format(),
                    'price' => (double) $ofr->price,
                    'old_price' => (double) $ofr->oldprice,
                    'url' => (string) $ofr->url,
                    'vendor' => isset($ofr->vendor) ? (string) $ofr->vendor : '',
                    'flag_is_available' => (string) $ofr->attributes()->available === 'true' ? 1 : 0,
                    'flag_is_delivery' => (string) $ofr->delivery === 'true' ? 1 : 0,
                    'flag_is_ready' => 0,
                    'flag_is_referral' => $this->getFlagIsReferral(),
                    'status' => ''
                ];

                $offer_vals['images'] = $this->getImages((array) $ofr->picture, $ofr_id);
            } else {
                $offer_vals = [
                    'currency' => (string) $ofr->currencyId,
                    'country_of_origin' => (string) $ofr->country_of_origin,
                    'description' => (string) $ofr->description,
                    'name' => implode(' ', $name),
                    'timestamp' => $timestamp_update->format(),
                    'price' => (double) $ofr->price,
                    'old_price' => (double) $ofr->oldprice,
                    'url' => (string) $ofr->url,
                    'vendor' => isset($ofr->vendor) ? (string) $ofr->vendor : '',
                    'flag_is_available' => (string) $ofr->attributes()->available === 'true' ? 1 : 0,
                    'flag_is_delivery' => (string) $ofr->delivery === 'true' ? 1 : 0,
                    'flag_is_referral' => $this->getFlagIsReferral(),
                    'status' => ''
                ];
            }

            if ($insert_mode === 'insert') {
                $insert_counter++;
                $offer->insert($offer_vals);
            } else {
                $update_counter++;
                $offer->update($offer_vals);
            }

            $params = $ofr->xpath('param');
            if ($params) {
                $_params = [];
                foreach ($params as $param) {
                    $_params[(string) $param->attributes()->name] = (string) $param;
                }

                $yml_param = new YmlParam();
                foreach ($_params as $k => $v) {
                    $yml_param = new YmlParam();
                    $yml_param->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_yml_offer = :id_yml_offer', 'name = :name'], [
                        ':name' => $k,
                        ':id_firm' => $this->getIdFirm(),
                        ':id_yml_offer' => $offer->id()
                    ])->objectByConds();

                    if (!$yml_param->exists()) {
                        $yml_param->insert([
                            'name' => $k,
                            'val' => $v,
                            'id_firm' => $this->getIdFirm(),
                            'id_yml_offer' => $offer->id()
                        ]);
                    } else {
                        $yml_param->update([
                            'val' => $v
                        ]);
                    }
                }
            }

            if ($i % 100 === 0) {
                app()->db()->query()->setText('UPDATE `yml` SET `offers_count_loaded` = `offers_count_loaded` + 100 WHERE id = :id')
                        ->execute([':id' => $this->getYmlFileId()]);
            }
        }
        app()->log('Оферы обработаны (' . $update_counter . ' обновлено, ' . $insert_counter . ' добавлено - из ' . $i . '/' . $i_ended . ' обработанных, всего: ' . count($offers) . ')', 1);

        $this->refreshPrice();
        app()->log('Прайсы обновлены', 1);

        app()->db()->query()->setText('UPDATE `yml` SET `offers_count_loaded` = :val WHERE id = :id')
                ->execute([':id' => $this->getYmlFileId(), ':val' => $i]);
    }

    public function refreshPrice() {
        $yml_offer = new \App\Model\YmlOffer();
        $price = new \App\Model\Price();
        $price->deleteRtIndexByIdFirm($this->getIdFirm());

        $limit = 1000;
        $offset = 0;
        $i = 0;
        $sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
        while (1) {
            $i++;
            if ($i % 1000 === 0) {
                app()->log($i);
            }
            $items = $price->reader()
                    ->setWhere(['AND', 'id_firm = :id_firm', 'source = :source'], [':id_firm' => $this->getIdFirm(), ':source' => 'ratiss'])
                    ->setLimit($limit, $offset)
                    ->setOrderBy('id ASC')
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->updateRtIndex($sphinx);
            }

            $offset += $limit;
        }

        $limit = 1000;
        $offset = 0;
        $i = 0;
        while (1) {
            $i++;
            if ($i % 1000 === 0) {
                app()->log($i);
            }
            $items = $yml_offer->reader()
                    ->setWhere(['AND', 'id_firm = :id_firm', 'status != :status'], [':id_firm' => $this->getIdFirm(), ':status' => 'deleted'])
                    ->setLimit($limit, $offset)
                    ->setOrderBy('id ASC')
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $price = new \App\Model\Price();
                $price->refreshByYmlOffer($item);
            }

            $offset += $limit;
        }

        return $this;
    }

    public function check($name_format = []) {
        $yml = $this->getXmlData();
        if ($yml) {
            $date = strtotime($yml->attributes()['date']);
            if ($date) {
                $yml_timestamp = new DateTime();
                $yml_timestamp->fromTimestamp($date);

                $yml_model = new Yml();
                $yml_model->reader()->setWhere(['OR', ['AND', 'hash = :hash', 'timestamp_yml >= :timestamp'], ['AND', 'status = :status', 'id_firm = :id_firm']], [':hash' => $this->getHash(), ':timestamp' => $yml_timestamp->format(), ':status' => 'processing', ':id_firm' => $this->getIdFirm()])
                        ->objectByConds();

                if (!$yml_model->exists()) {
                    $yml_model->insert([
                        'hash' => $this->getHash(),
                        'timestamp_yml' => $yml_timestamp->format(),
                        'id_firm' => $this->getIdFirm(),
                        'type' => $this->type,
                        'url' => $this->url,
                        'name_format' => Yml::setNameFormat($this->getNameFormat()),
                        'flag_is_referral' => $this->getFlagIsReferral()
                    ]);

                    $offers = $yml->xpath('/yml_catalog/shop/offers/offer');
                    $count = count($offers);
                    if ($count > 0) {
                        $yml_model->update([
                            'offers_count' => $count,
                            'offers_count_loaded' => 0,
                            'data' => $this->getData(),
                            'status' => '',
                            'name_format' => Yml::setNameFormat($this->getNameFormat()),
                            'flag_is_referral' => $this->getFlagIsReferral()
                        ]);
                    } else {
                        $this->setErrorCode(5);
                    }
                } else {
                    if ($yml_model->val('status') === 'processing') {
                        $this->setErrorCode(4);
                    } else {
                        $this->setErrorCode(3);
                    }
                }
            } else {
                $this->setErrorCode(2);
            }
        } else {
            $this->setErrorCode(1);
        }

        return $this->error_code === 0;
    }

    public function getErrorMessages() {
        return [
            1 => 'Мы не смогли обработать файл, возможно он не соответствует формату YML',
            2 => 'Дата и время обновления не указаны в файле',
            3 => 'Дата и время обновления, указанные в файле устарели. В базе данных содержится более новая версия',
            4 => 'В данный момент файл импортируется, дождитесь завершения процесса, чтобы загрузить новый',
            5 => 'В файле отсутствует блок <offers> с товарами/услугами',
        ];
    }

    public function getErrorMessage() {
        return $this->error_code ? $this->getErrorMessages()[$this->error_code] : '';
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getHash() {
        return $this->hash;
    }

    public function getData() {
        return $this->data;
    }

    public function getNameFormat() {
        return $this->name_format;
    }

    public function getFlagIsReferral() {
        return (int) $this->flag_is_referral;
    }

    public function getIdFirm() {
        return $this->id_firm;
    }

    public function getYmlFileId() {
        return $this->id_yml_model;
    }

    public function getImages($images_urls, $id_yml) {
        $result = [];
        if ($images_urls) {
            foreach ($images_urls as $url) {
                $dir = new Dir(APP_DIR_PATH . '/public/yml_image');
                $dir->create();
                $this->fileUploader()->setDirPath($dir->getPath());
                $result = ['success' => false];

                try {
                    $this->fileUploader()->uploadRemoteFile($url);
                    $file_data = $this->fileUploader()->getFileData();
                    $vals = [
                        'file_dimension_size' => $file_data['for_model']['file_dimension_size'],
                        'file_extension' => $file_data['for_model']['file_extension'],
                        'file_name' => $file_data['for_model']['file_name'],
                        'file_raw_name' => $file_data['for_model']['file_raw_name'],
                        'file_subdir_name' => $file_data['for_model']['file_subdir_name'],
                        'id_firm' => $this->getIdFirm(),
                        'id_yml' => $id_yml,
                        'base_path' => '/yml_image'
                    ];

                    $image = new YmlImage();
                    $image->embededFileComponent()->setSubDirName('yml_image');
                    $image->insert($vals);

                    try {
                        if ($image->embeddedFile()->isImage()) {
                            $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                                    ->setTargetFilePath($image->embeddedFile()->path('-160x160'))
                                    ->setTargetFileWidth(160)
                                    ->setTargetFileHeight(160)
                                    ->setWithCutoff(false)
                                    ->resize();

                            $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                                    ->setTargetFilePath($image->embeddedFile()->path('-260x260'))
                                    ->setTargetFileWidth(260)
                                    ->setTargetFileHeight(260)
                                    ->setWithCutoff(false)
                                    ->
                                    resize();
                        }
                    } catch (\ImagickException $exc) {
                        continue;
                    }

                    $result[] = 'yml-image~' . $image->id();
                } catch (\Sky4\Exception $exc) {
                    continue;
                }
            }
        }

        return $result ? implode(',', $result) : '';
    }

    /**
     * 
     * @return FileUploader
     */
    protected function fileUploader() {
        if ($this->file_uploader === null) {
            $this->file_uploader = new FileUploader();
            $this->file_uploader->
                    setMaxFileSize(25 * 1024 * 1024)
                    ->setMinFileSize(1)
                    ->setUseGenSubdirs(true)
                    ->setUseGenFileName(true);
        }

        return $this->file_uploader;
    }

    protected function imageProcessor() {
        if ($this->image_processor === null) {
            $this->image_processor = class_exists('Imagick', false) ? new Imagick() : new Gd();
        }
        return $this->image_processor;
    }

    protected function compareCategories() {
        $yc = new YmlCategory();
        $categories = $yc->reader()->setWhere(['AND', 'id_firm = :id_firm', 'parent_node != :nil', 'flag_is_fixed != :one'], [':nil' => 0, ':id_firm' => $this->getIdFirm(), ':one' => 1])
                ->setOrderBy('parent_node ASC, id_yml_category ASC')
                ->objects();

        $sphinx = SphinxQL::create(app()->getSphinxConnection());
        $res = [];
        foreach ($categories as $cat) {
            $name = preg_replace('~[^a-zA-Zа-яА-Я0-9- ]~u', '', $cat->name());
            app()->log('Сравнение категории ' . str_replace([')', '(', '/'], ' ', $name), 1);
            $data = $sphinx->select(['id_catalog', 'node_level', SphinxQL::expr('WEIGHT() AS weight')])
                    ->from(SPHINX_PRICE_CATALOG_INDEX)
                    ->where('id_group', '!=', 44)
                    ->option('max_matches', SPHINX_MAX_INT)
                    ->limit(0, SPHINX_MAX_INT)
                    ->groupby('id_catalog')
                    ->orderby('weight', 'DESC')
                    ->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
                    ->match('web_many_name', SphinxQL::expr(str_replace([')', '(', '/'], ' ', $name)))
                    ->execute();

            $parent = new YmlCategory();
            $parent->reader()->setWhere(['AND', 'id_yml_category = :node', 'id_firm = :id_firm'], [':node' => $cat->val('parent_node'), ':id_firm' => $this->getIdFirm()])
                    ->objectByConds();

            $res[$cat->id()] = [
                'name' => $parent->name() . '/' . $cat->name()
            ];

            $cats = [];
            $_cats = [];
            $id_catalog = 0;
            if ($data) {
                $min_level = 10;
                foreach ($data as $dt) {
                    $pcat = new PriceCatalog($dt['id_catalog']);
                    $current_level = (int) $pcat->val('node_level');
                    $min_level = $current_level < $min_level ? $current_level : $min_level;
                    $cats[] = [
                        'level' => $current_level,
                        'name' => $pcat->name(),
                        'id' => $pcat->id(),
                        'parent_node' => $pcat->val('parent_node')
                    ];
                }

                foreach ($cats as $pkey => $pcat) {
                    if ($pcat['level'] > $min_level) {
                        unset($cats[$pkey]);
                    }
                }

                if (count($cats) > 1) {
                    $parent = current($_cats)['parent_node'];
                    $ncat = new PriceCatalog($parent);
                    $cats = [];
                    $cats[] = [
                        'level' => $ncat->val('node_level'),
                        'name' => $ncat->name(),
                        'id' => $ncat->id(),
                        'parent_node' => $ncat->val('parent_node')
                    ];
                }

                $_cats = [];
                foreach ($cats as $pkey => $pcat) {
                    $_cats[$pcat['id']] = $pcat['name'];
                    if (trim($pcat['name']) === trim($name)) {
                        $_cats = [];
                        $_cats[$pcat['id']] = $name;
                        break;
                    }
                }

                $id_catalog = key($_cats);
            }

            $cat->update([
                'id_catalog' => $id_catalog
            ]);
        }
    }
    
    public function getCRC32Checksum($input)
    {
        return abs(crc32($input));
    }
}
