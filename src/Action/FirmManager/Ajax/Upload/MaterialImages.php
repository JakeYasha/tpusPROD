<?php

namespace App\Action\FirmManager\Ajax\Upload;

use App\Model\MaterialFile;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class MaterialImages extends \App\Action\FirmManager\Ajax\Upload {

    public function execute() {
        $params = app()->request()->processPostParams([
            'material_id' => ['type' => 'int'],
        ]);

        if (app()->FirmManager()->isNewsEditor()) {
            $dir = new Dir(APP_DIR_PATH . '/public/service/' . app()->FirmManager()->id_service() . '/material/file');
            $dir->create();
            
            $this->fileUploader()->setFileDirPath($dir->getPath());

            $result = ['success' => false];

            if ($this->fileUploader()->uploadFile()) {
                $file_data = $this->fileUploader()->getFileData();
                $vals = [
                    'file_dimension_height' => $file_data['dimension_height'],
                    'file_dimension_size' => $file_data['dimension_size'],
                    'file_dimension_width' => $file_data['dimension_width'],
                    'file_extension' => $file_data['extension'],
                    'file_name' => $file_data['name'],
                    'file_raw_name' => $file_data['raw_name'],
                    'file_subdir_name' => implode('/', $file_data['subdirs_names']),
                    'type' => 'image',
                    'material_id' => $params['material_id'],
                    'id_service' => app()->FirmManager()->id_service(),
                    'flag_is_temp' => 0
                ];

                $image = new MaterialFile();
                $image->embededFileComponent()->setSubDirName('service/' . app()->FirmManager()->id_service() . '/material/file');
                $image->insert($vals);

                if ($image->embeddedFile()->isImage()) {
                    $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                            ->setTargetFilePath($image->embeddedFile()->path('-thumb'))
                            ->setTargetFileWidth(330)
                            ->setTargetFileHeight(220)
                            ->setWithCutoff(false)
                            ->resize();

                    $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                            ->setTargetFilePath($image->embeddedFile()->path('-140x95'))
                            ->setTargetFileWidth(140)
                            ->setTargetFileHeight(95)
                            ->setWithCutoff(false)
                            ->resize();

                    $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                            ->setTargetFilePath($image->embeddedFile()->path('-90x60'))
                            ->setTargetFileWidth(90)
                            ->setTargetFileHeight(60)
                            ->setWithCutoff(false)
                            ->resize();
                    
                    if ($image->val('file_extension') !== 'gif' && (int) $image->val('file_dimension_width') > 1000) {
                        $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                                ->setTargetFilePath($image->embeddedFile()->path())
                                ->setTargetFileWidth(1000)
                                ->setTargetFileHeight(null)
                                ->setWithCutoff(true)
                                ->resize();
                    }

                    $result = [
                        'success' => true,
                        'thumb_path' => $image->embededFileComponent()->iconLink('-thumb'),
                        'full_path' => $image->embededFileComponent()->iconLink(),
                        'composite_id' => 'material-file~' . $image->id(),
                        'image_id' => $image->id()
                    ];
                }
            }
        }

        die(json_encode($result));
    }

}
