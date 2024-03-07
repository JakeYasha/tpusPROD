<?php

namespace App\Model;

use Sky4\Model\Utils;

class Parsetable extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait,
        Component\NameTrait,
        Component\ImageTrait,
        Component\TimestampActionTrait;

    public function fields() {
        return [
            
        ];
    }
    
    
    
    
    public static function prepare(Material $item) {
        
		return [
			
		];
	}
}
