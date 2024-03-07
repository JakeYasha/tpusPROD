<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();

$img_data = [];

foreach ($img_data as $_image_data) {
    $embedded_image = '/var/www/sites/tovaryplus.ru/update' . str_replace([
                '/var/www/sites/tovaryplus.ru/update',
                '/usr/share/nginx/html/tovaryplus.new/tovaryplus.ru/app/cron'
                    ], '', trim($_image_data['image']));
    $price_object = new \App\Model\Price();
    $price_object->reader()
            ->setWhere(['AND', 'legacy_id_firm = :id_firm', 'legacy_id_price= :id_price', 'legacy_id_service= :id_service'], 
                    [':id_firm' => $_image_data['id_firm'], ':id_price' => $_image_data['id_price'], ':id_service' => $_image_data['id_service']])
            ->objectByConds();

    if ($embedded_image && file_exists($embedded_image) && is_file($embedded_image)) {
        setEmbeddedImage($price_object, $embedded_image);
    }
}

function setEmbeddedImage(\App\Model\Price $price, $embedded_image) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $file_extension = array_reverse(explode('.', $embedded_image))[0];
    $image = [
        'file_name' => \Sky4\Helper\Random::uniqueId(),
        'id_firm' => $price->id_firm(),
        'id_price' => $price->id(),
        'legacy_id_firm' => $price->val('legacy_id_firm'),
        'legacy_id_price' => $price->val('legacy_id_price'),
        'legacy_id_service' => $price->val('legacy_id_service'),
        'file_extension' => $file_extension,
        'path' => $embedded_image,
        'source' => 'auto'
    ];

    $where = ['AND', 'id_firm = :id_firm', 'id_price = :id_price', 'source = :source'];
    $params = [':id_firm' => (int) $image['id_firm'], ':id_price' => (int) $image['id_price'], ':source' => 'auto'];

    $im = new \App\Model\Image();
    $im->reader()
            ->setWhere($where, $params)
            ->objects();

    //копирование и подготовка изображения
    $target_file_name = $image['file_name'];
    $target_dir = new \Sky4\FileSystem\Dir(APP_DIR_PATH . '/public/image/');
    $target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 0, 1) . '/');
    if (!$target_dir->exists()) {
        $target_dir->create();
    }

    $target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 1, 1) . '/');
    if (!$target_dir->exists()) {
        $target_dir->create();
    }

    $image['file_subdir_name'] = str()->sub($target_file_name, 0, 1) . '/' . str()->sub($target_file_name, 1, 1);
    copy($image['path'], $target_dir->path() . $target_file_name . '.' . $image['file_extension']);
    unset($image['path']);

    if ($im->exists()) {
        $file = new \Sky4\FileSystem\File(APP_DIR_PATH . '/public/image/' . $im->val('file_subdir_name') . '/' . $im->val('file_name') . '.' . $im->val('file_extension'));
        $file->remove();
        $im->update($image);
    } else {
        $im->insert($image);
    }
}
