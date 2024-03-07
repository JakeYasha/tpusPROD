<?php

namespace App\Action\FirmUser\Ajax\Upload;

use App\Model\FirmFile;
use App\Model\FirmPromo;
use Sky4\Exception;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class FirmPromoImage extends \App\Action\FirmUser\Ajax\Upload {

    public function execute() {
        $get = app()->request()->processGetParams([
            'id' => ['type' => 'int'],
        ]);

        $fp = new FirmPromo($get['id']);
        if (!app()->firmUser()->exists() || ($fp->exists() && !($fp->id_firm() === app()->firmUser()->id_firm() && $fp->id_service() === app()->firmUser()->id_service()))) {
            throw new Exception();
        }

        $dir = new Dir(APP_DIR_PATH . '/public/file');
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
                'type' => 'promo-image',
                'id_firm' => app()->firmUser()->id_firm(),
                'flag_is_temp' => 1
            ];

            $image = new FirmFile();
            $image->embededFileComponent()->setSubDirName('file');
            $image->insert($vals);

            if ($image->embeddedFile()->isImage()) {
                $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                        ->setTargetFilePath($image->embeddedFile()->path('-150x150'))
                        ->setTargetFileWidth(300)
                        ->setTargetFileHeight(300)
                        ->setWithCutoff(false)
                        ->resize();

                $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                        ->setTargetFilePath($image->embeddedFile()->path('-300x177'))
                        ->setTargetFileWidth(300)
                        ->setTargetFileHeight(177)
                        ->setWithCutoff(true)
                        ->resize();

                $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                        ->setTargetFilePath($image->embeddedFile()->path('-300x177'))
                        ->setTargetFileWidth(320)
                        ->setTargetFileHeight(180)
                        ->setWithCutoff(false)
                        ->resize();
                
                $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                        ->setTargetFilePath($image->embeddedFile()->path('-320x180'))
                        ->setTargetFileWidth(320)
                        ->setTargetFileHeight(180)
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
                    'thumb_path' => $image->embededFileComponent()->iconLink('-300x177'),
                    'image_id' => $image->id(),
                    'composite_id' => 'firm-file~' . $image->id()
                ];
            }
        }

        die(json_encode($result));
    }

}
