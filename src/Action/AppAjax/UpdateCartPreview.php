<?php

namespace App\Action\AppAjax;

class UpdateCartPreview extends \App\Action\AppAjax {

    public function execute() {
        $preview = new \App\Action\Cart\Preview();
        die($preview->execute());
    }

}
