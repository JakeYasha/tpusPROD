<?php

namespace App\Model;
class AdvertRestrictions extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;
	
	public function title() {
		return $this->exists() ? $this->name() : 'Рекламные ограничения';
	}
        
        public function image() {
                if ($this->exists()) {
                    $file_name = md5($this->name());
                    $file_path = '/public/img/' . $file_name . '.png';
                    if (!file_exists(APP_DIR_PATH . $file_path)) {
                        GDTool::getImage($this->name(), $this->id());
                    }
                    
                    return $file_path;
                }
                
		return '';
	}
}
