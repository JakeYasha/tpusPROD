<?php

namespace App\Action\FirmManager;

use Sky4\Exception;
use function app;

class Ajax extends \App\Action\FirmManager {

    use \App\Classes\Traits\Ajax;

    public function execute() {
        throw new Exception();
    }

}
