<?php

namespace App\Action\Adv;

class Item extends \App\Action\Adv {

	public function execute($id = 0) {
		$get = app()->request()->processGetParams(['fix' => ['type' => 'int']]);
		$banner = new \App\Model\Banner();
		$banner->reader()->object($id);
		if ($banner->exists() && $banner->isActive()) {
			$banner->setVal('url', app()->metadata()->replaceLocationTemplates($banner->val('url')));
			app()->stat()->fixBannerClick($banner);
			if ($get['fix'] !== 1) {
				app()->response()->redirect($banner->val('url'));
			} else {
				exit();
			}
		}

		throw new \Sky4\Exception();
	}

}
