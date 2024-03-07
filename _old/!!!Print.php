<?php

require_once APP_DIR_PATH . '/app/components/mpdf/mpdf.php';

namespace App\Controller;
class Print extends \App\Classes\Controller {

	public function actionIndex() {
		app()->frontController()->layout()->setTemplate('print');
		$url = app()->request()->processGetParams(['print_url' => ['type' => 'string']])['print_url'];
		if (!$url) {
			throw new CException(CException::TYPE_BAD_URL);
		}
		$content = file_get_contents(APP_URL . $url);
		$html = $this->view()
				->setTemplate('index', 'print')
				->set('content', $content)
				->render();

		$mpdf = new mPDF('utf-8', 'A4', '10', 'Arial', 10, 10, 7, 7, 10, 10);
		$stylesheet = file_get_contents(APP_DIR_PATH . '/public/css/pdf.css');
		$mpdf->WriteHTML($stylesheet, 1);

		$mpdf->list_indent_first_level = 0;
		$mpdf->WriteHTML($html, 2); /* формируем pdf */
		$mpdf->Output('print.pdf', 'I');
	}

}
