<?php

namespace App\Action\FirmManager;
require_once APP_DIR_PATH . '/protected/mpdf/src/functions.php';

use App\Action\FirmManager\Calls;
use mPDF;
use function app;

class Statistics727373Pdf extends \App\Action\FirmManager {

	public function execute() {
		$action = new Statistics727373();
		$content = $action->execute(true);

		$html = $this->view()
				->set('content', $content)
				->set('manager', app()->firmManager())
				->setTemplate('statistics727373_pdf')
				->render();

		$mpdf = new \Mpdf\Mpdf(['utf-8', 'A4', '10', 'Arial', 10, 10, 7, 7, 10, 10]);
		$stylesheet = file_get_contents(APP_DIR_PATH . '/public/css/pdf.css');
		$mpdf->WriteHTML($stylesheet, 1);

		$mpdf->list_indent_first_level = 0;
		$mpdf->WriteHTML($html, 2); /* формируем pdf */
		$mpdf->Output('запросы_727373.pdf', 'I');
		exit();
	}

}
