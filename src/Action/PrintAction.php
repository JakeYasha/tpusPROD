<?php

namespace App\Action;
require_once APP_DIR_PATH . '/protected/mpdf/src/functions.php';

use App\Classes\Action;
use mPDF;
use Sky4\Exception;

class PrintAction extends Action {

	public function execute() {
		app()->frontController()->layout()->setTemplate('print');
		$url = app()->request()->processGetParams(['print_url' => ['type' => 'string']])['print_url'];
		if (!$url) {
			throw new Exception();
		}

		$content = file_get_contents(APP_URL . $url);
		$html = $this->view()
				->setTemplate('index', 'print')
				->set('content', $content)
				->render();

		$mpdf = new \Mpdf\Mpdf(['utf-8', 'A4', '10', 'Arial', 10, 10, 7, 7, 10, 10]);
		$stylesheet = file_get_contents(APP_DIR_PATH . '/public/css/pdf.css');
		$mpdf->WriteHTML($stylesheet, 1);

		$mpdf->list_indent_first_level = 0;
		$mpdf->WriteHTML($html, 2); /* формируем pdf */
		$mpdf->Output('print.pdf', 'I');
	}

}
