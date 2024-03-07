<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Kit as KitModel;
//use App\Model\StsService\FormAdd;
use function app;

class Kit extends FirmManager {

	public function execute($id_kit = 0) {
		if (app()->firmManager()->isNewsEditor()) {
            if ($id_kit) {
                $kit = new KitModel($id_kit);
                if ($kit->exists()) {
                    app()->metadata()->setTitle('Конструктор подборки № ' . $kit->val('number'));
                    app()->breadCrumbs()->setElem('Список подборок', self::link('/kits/'));
                    app()->breadCrumbs()->setElem($kit->val('name'), self::link('/kit/'));
                } else {
                    app()->response()->redirect('/firm-manager/kit/');
                }
                
                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                        ->set('kit', $kit)
                        ->setTemplate('kit_edit', 'firmmanager')
                        ->save();
            } else {
                app()->metadata()->setTitle('Конструктор новой подборки');
                app()->breadCrumbs()->setElem('Список подборок', self::link('/kits/'));
                app()->breadCrumbs()->setElem('Новая подборка', self::link('/kit/'));

                $kit = new KitModel();
                $last_kit = $kit->reader()
                        ->setSelect('MAX(number) as max_number')
                        ->rowByConds();
                
                $last_kit_number = 0;
                if (isset($last_kit[0])) {
                    $last_kit_number = $last_kit[0]['max_number'];
                    $last_kit_number++;
                }
                $last_kit_number++;
                        
                $kit = new KitModel();
                $kit->setVal('number', $last_kit_number);

                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                        ->set('kit', $kit)
                        ->setTemplate('kit', 'firmmanager')
                        ->save();
            }
		}

		return $this;
	}

}
