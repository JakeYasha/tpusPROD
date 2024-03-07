<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\StsHistCalls;
use App\Model\Firm;
use App\Model\FirmManager as FirmManagerModel;
use App\Model\PriceCatalog;
use App\Presenter\FirmUserStatistics;
use App\Classes\PHPExcel;
use App\Classes\PHPExcel\PHPExcel_IOFactory;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Alignment;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Border;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Color;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Fill;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;

class Calls extends FirmManager {

	public function execute($html_mode = false) {
		app()->breadCrumbs()->setElem('Звонки', self::link('/calls/'));

		$this->params = app()->request()->processGetParams([
			'id_manager' => ['type' => 'int', 'default_val' => 0],
			'id_firm' => ['type' => 'int', 'default_val' => 0],
			'readdress_only' => ['type' => 'string', 'default_val' => 'off'],
			't_start' => ['type' => 'string', 'default_val' => date('Y-m-d', strtotime('-1 month'))],
			't_end' => ['type' => 'string', 'default_val' => date('Y-m-d', time())],
            'export' => ['type' => 'string'],
		]);

        $current_id_service = app()->firmManager()->id_service();

		$this->text()->getByLink('firm-manager/calls');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Звонки');
		}
        
        if (!isset($this->params['id_service'])) {
            $this->params['id_service'] = $current_id_service;
        }
		$this->params['html_mode'] = $html_mode ? true : false;

		$presenter = new FirmUserStatistics();

		$presenter->setLimit(20);
		if ($html_mode) {
			$presenter->setLimit(500);
		} else if ($this->params['export'] === 'xls') {
			$presenter->setLimit(5000);
		}
        
        if ($this->params['id_manager'] || $this->params['id_firm']) {
            $presenter->findOnlineCalls($this->params);
        }

		$managers = [];
		$managers = $this->model()->reader()
				->setWhere(['AND', 'id_service = :id_service', 'type != :type'], [':id_service' => $current_id_service, ':type' => 'service'])
				->setOrderBy('name ASC')
				->objects();

		$firms = [];
        $firm = new Firm();
        $shc = new \App\Model\StsHistAnswer();
        $firm_ids = array_keys($shc->reader()->setSelect('id_firm')
						->setWhere(['AND', 'id_service = :id_service', 'id_hist_answer > :id_hist_answer'], [':id_hist_answer' => 78398349, ':id_service' => $current_id_service])
						->rowsWithKey('id_firm'));
		$firm_ids_conds = Utils::prepareWhereCondsFromArray(array_unique($firm_ids), 'id');

        if (count($firm_ids) > 0) {
			$firm_where = ['AND', 'id_service = :id_service'];
			$firm_params = [':id_service' => $current_id_service];
			$firm_conds = Utils::prepareWhereCondsFromArray($firm_ids, 'id');
			$firm_where[] = $firm_conds['where'];
			$firm_params += $firm_conds['params'];

			$firms = $firm->reader()
					->setWhere($firm_where, $firm_params)
					->setOrderBy('company_name ASC')
					->objects();
		}

		if ($html_mode) {
			return $presenter->renderItems();
		}
        
        if ($this->params['export'] === 'xls') {
			$this->exportCalls($presenter->getItems());
			exit();
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('firms', $firms)
				->set('filters', $this->params)
				->set('items', ($this->params['id_manager'] || $this->params['id_firm']) ? $presenter->renderItems() : 'Выберите фирму или специалиста')
				->set('items_count', $presenter->pagination()->getTotalRecords())
				->set('pagination', $presenter->pagination()->render(true))
				->set('managers', $managers)
				->setTemplate('calls')
				->save();
	}
    
    private function exportCalls($data) {
        $title = $this->params['readdress_only'] == 'on' ? 'Отчет по переадресациям' : 'Отчет по звонкам';
        $type = $this->params['readdress_only'] == 'on' ? 'Переадресации' : 'Звонки';

		//fonts
		$base_font = ['font' => ['name' => 'MS Sans Serif', 'size' => '8', 'bold' => FALSE]];
		$th_fonr = ['font' => ['name' => 'MS Sans Serif', 'size' => '8', 'bold' => TRUE]];
		$h_font = ['font' => ['name' => 'MS Sans Serif', 'size' => '14', 'bold' => FALSE]];
		$h_font_b = ['font' => ['name' => 'MS Sans Serif', 'size' => '14', 'bold' => TRUE]];

		//pos
		$center = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP]];
		$right = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP]];

		$border_dashed = ['borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_DASHED]]];

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('MS Sans Serif');
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(TRUE);
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
		$objPHPExcel->getProperties()->setCreator("TovaryPlus.ru")->setLastModifiedBy("TovaryPlus.ru")->setTitle($type)->setSubject($type)->setDescription($type);
		$sheet = $objPHPExcel->getActiveSheet();

		$bgColor = new PHPExcel_Style_Color();
		$bgColor->setRGB("C0C0C0");
		$sheet->getStyle("A6:F6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle("A6:F6")->getFill()->setStartColor($bgColor);

		$sheet->getDefaultRowDimension()->setRowHeight(12.75);
		$sheet->getRowDimension('2')->setRowHeight(19.50);

		$sheet->getStyle('A1')->applyFromArray($base_font)->applyFromArray($right);
		$sheet->setCellValue('A1', 'Товары плюс '.date("d.m.Y"));
		$sheet->getStyle('A2')->applyFromArray($h_font)->applyFromArray($center);

		$sheet->mergeCells('A1:F1');
		$sheet->mergeCells('A2:F2');

        
        if ($this->params['id_manager']) {
            $manager = new FirmManagerModel($this->params['id_manager']);
            if ($manager->exists()) {
                $sheet->setCellValue('B3', "Специалист: " . $manager->name());
            }
        } else if ($this->params['id_firm']) {
            $firm = new Firm($this->params['id_firm']);
            if ($firm->exists()) {
                $sheet->setCellValue('B3', "Фирма: " . $firm->name());
            }
        }
		$sheet->setCellValue('B5', "Период: с " . $this->params['t_start'] . " по " . $this->params['t_end']);

		$sheet->mergeCells('B3:F3');
		$sheet->mergeCells('B4:F4');
		$sheet->mergeCells('B5:F5');

		$sheet->getStyle("A6:F6")->applyFromArray($th_fonr);

		$sheet->setCellValue('A2', $title);
		$sheet->setTitle($type);

		$sheet->getColumnDimension('A')->setWidth(4.57);
		$sheet->getColumnDimension('B')->setWidth(35);
		$sheet->getColumnDimension('C')->setWidth(35);
		$sheet->getColumnDimension('D')->setWidth(55);
		$sheet->getColumnDimension('E')->setWidth(55);
		$sheet->getColumnDimension('F')->setWidth(35);

		$sheet->setCellValue('A6', '№');
		$sheet->setCellValue('B6', 'Дата/Время звонка');
		$sheet->setCellValue('C6', 'Диспетчер');
		$sheet->setCellValue('D6', 'Наименование организации');
		$sheet->setCellValue('E6', 'Наименование товара/услуги');
		$sheet->setCellValue('F6', 'Переадресация/Результат');

		$i = 6;

		foreach ($data['items'] as $timestamp => $firms) {
            foreach ($firms as $firm => $_items) {
                foreach ($_items as $item) {
                    $i++;
                    $sheet->setCellValue('A'.$i, ($i - 6));
                    $sheet->setCellValue('B'.$i, date('d.m.Y H:i:s', $timestamp));
                    $sheet->setCellValue('C'.$i, $item['dispatcher']);
                    $sheet->setCellValue('D'.$i, $item['firm_name']);
                    $sheet->setCellValue('E'.$i, $item['firm_name'] == $item['name'] ? '-' : $item['name']);
                    $sheet->setCellValue('F'.$i, $item['phone'] ? ($item['phone'] . ' / ' . $item['readdress']) : '-');
                    $sheet->getRowDimension($i)->setRowHeight(31.5);
                }
            }
        }
		$sheet->getStyle('A7:F'.$i)->applyFromArray($border_dashed);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$type.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter->save('php://output');
		exit();
	}

}
