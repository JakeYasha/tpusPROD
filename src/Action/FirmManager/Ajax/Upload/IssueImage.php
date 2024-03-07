<?php

namespace App\Action\FirmManager\Ajax\Upload;

use App\Model\Issue;
use App\Model\IssueFile;
use Sky4\Exception;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class IssueImage extends \App\Action\FirmManager\Ajax\Upload {

    public function execute() {
        $params = app()->request()->processPostParams([
            'model_id' => ['type' => 'int'],
        ]);
        
        if (app()->FirmManager()->isNewsEditor()) {
            $dir = new Dir(APP_DIR_PATH . '/public/service/' . app()->FirmManager()->id_service() . '/issue/file');
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
                    'issue_id' => $params['model_id'],
                    'id_service' => app()->FirmManager()->id_service(),
                    'flag_is_temp' => 0
                ];

                $image = new IssueFile();
                $image->embededFileComponent()->setSubDirName('service/' . app()->FirmManager()->id_service() . '/issue/file');
                $image->insert($vals);

                if ($image->embeddedFile()->isImage()) {
                    $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                            ->setTargetFilePath($image->embeddedFile()->path('-thumb'))
                            ->setTargetFileWidth(300)
                            ->setTargetFileHeight(190)
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
                        'image_id' => $image->id(),
                        'composite_id' => 'issue-file~' . $image->id(),
                        'image_type' => 'issue-full-image'
                    ];
                }
            }
        }

        die(json_encode($result));
    }

}