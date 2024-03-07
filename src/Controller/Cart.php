<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\Cart as CartModel;

class Cart extends Controller {

    /**
	 * 
	 * @return Cart
	 */
	public function model() {
		if ($this->model === null) {
                        $_cart = new CartModel();
			$this->model = $_cart->getByCookie();
		}
		return $this->model;
	}
    
    public function renderPreview() {
        $preview = new \App\Action\Cart\Preview();
        return $preview->execute();
    }

}
