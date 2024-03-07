<?php

namespace App\Action\FirmManager\Ajax\Image;

use App\Model\MaterialFile;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class ImageList extends \App\Action\FirmManager\Ajax\Image {

    public function execute() {
        $params = app()->request()->processPostParams([
			'date_begin' => ['type' => 'string'],//'2019-10-18',
			'date_end' => ['type' => 'string'],//'2019-11-18', 
        ]);

        $result = [];
        if (app()->FirmManager()->isNewsEditor()) {
            $image = new MaterialFile();
            if (isset($params['date_begin']) && $params['date_begin'] && isset($params['date_end']) && $params['date_end']) {
                $images = $image->reader()
                        ->setWhere([
                            'AND', 
                            'id_service = :id_service',
                            'timestamp_inserting > :date_begin', 
                            'timestamp_inserting < :date_end'
                        ],[
                            ':id_service' => app()->FirmManager()->id_service(),
                            ':date_begin' => $params['date_begin'],
                            ':date_end' => $params['date_end'],
                        ])
                        ->setOrderBy('id DESC')
                        ->setLimit(100)
                        ->objects();
            } else {
                $images = $image->reader()
                        ->setWhere([
                            'AND', 
                            'id_service = :id_service',
                        ],[
                            ':id_service' => app()->FirmManager()->id_service(),
                        ])
                        ->setLimit(100)
                        ->setOrderBy('id DESC')
                        ->objects();
            }
            
            if ($images) {
                foreach($images as $image) {
                    $result []= [
                        'success' => true,
                        'thumb_path' => $image->embededFileComponent()->setSubDirName('service/' . app()->FirmManager()->id_service() . '/material/file')->iconLink('-thumb'),
                        'full_path' => $image->embededFileComponent()->setSubDirName('service/' . app()->FirmManager()->id_service() . '/material/file')->iconLink(),
                        'name' => $image->val('file_raw_name'),
                        'extension' => $image->val('file_extension'),
                        'composite_id' => 'material-file~' . $image->id(),
                        'image_id' => $image->id()
                    ];
                }
            }
        }

        die(json_encode($result));
    }

}
