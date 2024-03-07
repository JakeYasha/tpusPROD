<?php
namespace App\Action\Utils;
use App\Model\FirmPromo;
use Sky4\Model\Utils;
ini_set('max_execution_time', -1);

class ChangeFirmPromoThumbs extends \App\Action\Utils {
    
    protected $image_processor = null;
    
	public function __construct() {
		parent::__construct();
		if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
        $firm_promo = new FirmPromo();
        $firm_promos = $firm_promo->reader()
                ->setWhere(['AND', 'flag_is_active = :1'], [':1' => 1])
                ->setOrderBy('timestamp_inserting DESC')
                ->objects();
        
        $files = [];
		foreach ($firm_promos as $firm_promo) {
			$files[] = Utils::getFirstCompositeId($firm_promo->val('image'));
		}

		$files = Utils::getObjectsByIds($files);
        
        $items = [];
        foreach($firm_promos as $firm_promo) {
            $image_key = Utils::getFirstCompositeId($firm_promo->val('image'));
            if (isset($files[$image_key])) {
                $image = $files[$image_key];
                // ПЕРВЫЙ РАЗ СО СТРОКОЙ, ВТОРОЙ РАЗ БЕЗ НЕЕ
                //$image->embededFileComponent()->setSubDirName('file');
                echo '<br/>' . $image->id() . ': - ' . $image->embeddedFile()->path() . ' - ' . (file_exists($image->embeddedFile()->path())? 'EXISTS' : 'NOT EXISTS');
                if (file_exists($image->embeddedFile()->path()) && !file_exists($image->embeddedFile()->path('-320x180')) && $image->embeddedFile()->isImage()) {
                    $this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
                            ->setTargetFilePath($image->embeddedFile()->path('-320x180'))
                            ->setTargetFileWidth(320)
                            ->setTargetFileHeight(180)
                            ->setWithCutoff(false)
                            ->resize();
                    echo '<br/>' . $image->id() . ' - DONE';
                }
            }
			$items[] = \App\Model\FirmPromo::prepare($firm_promo, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
        }
        
        foreach($items as $item) {
            echo '<br/><img border="1" src="' . $item['image'] . '"/>';
        }
        
		exit();
	}
    
    protected function imageProcessor() {
		if ($this->image_processor === null) {
			$this->image_processor = class_exists('Imagick', false) ? new \Sky4\Component\ImageProcessor\Imagick() : new \Sky4\Component\ImageProcessor\Gd();
		}
		return $this->image_processor;
	}

}
