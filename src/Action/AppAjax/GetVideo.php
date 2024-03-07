<?php

namespace App\Action\AppAjax;

class GetVideo extends \App\Action\AppAjax {

	public function execute($id_video) {
		$fv = new \App\Model\FirmVideo();
		$fv->get($id_video);
		$fv->setVal('video_code', html_entity_decode($fv->val('video_code')));

		$this->view()
				->set('item', $fv)
				->setTemplate('video', 'elem');

		die($this->view()->render());
	}

}
