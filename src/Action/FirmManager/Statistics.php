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

class Statistics extends FirmManager {

	public function execute($html_mode = false) {
		app()->breadCrumbs()->setElem('Запросы', self::link('/statistics/'));

		$this->params = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int', 'default_val' => 0],
			't_start' => ['type' => 'string', 'default_val' => date('Y-m-d', strtotime('-1 month'))],
			't_end' => ['type' => 'string', 'default_val' => date('Y-m-d', time())],
            'export' => ['type' => 'string'],
            'group' => ['type' => 'int', 'default_val' => 0],
		]);

        if ($this->params['export'] === 'xls') {
            $this->params['t_start'] = strtotime($this->params['t_start']);
            $this->params['t_end'] = strtotime($this->params['t_end']);
        }
        
        $current_id_service = app()->firmManager()->id_service();

		$this->text()->getByLink('firm-manager/statistics');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Запросы');
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

        if ($this->params['id_firm']) {
            if ($this->params['export'] === 'xls') {
                $presenter->findPages($this->params);
            } else {
                $presenter->findStatistics($this->params);
            }
        }

        $__conds = Utils::prepareWhereCondsFromArray(app()->firmManager()->getManagerUserIds(), 'id_manager');
        $_where = [
            'AND',
            'flag_is_active = :1',
            $__conds['where']
        ];
        $_params = [
            ':1' => 1
        ] + $__conds['params'];

        $firm = new Firm();
        $firms = $firm->reader()
                ->setWhere($_where, $_params)
                ->setOrderBy('company_name ASC')
                ->setLimit(2000)
                ->objects();

        if ($html_mode) {
			return $presenter->renderItems();
		}
        
        if ($this->params['export'] === 'xls') {
            $this->params['t_start'] = date('Y-m-d', $this->params['t_start']);
            $this->params['t_end'] = date('Y-m-d', $this->params['t_end']);
			$this->exportStatistics($presenter->getItems());
			exit();
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('firms', $firms)
				->set('filters', $this->params)
				->set('items', ($this->params['id_firm']) ? $presenter->renderItems() : 'Выберите фирму')
				->set('items_count', $presenter->pagination()->getTotalRecords())
				->set('pagination', $presenter->pagination()->render(true))
				->setTemplate('statistics')
				->save();
	}
    
    private function exportStatistics($data) {
        $our_title = 'Информационный центр "Товары Плюс"';
        $our_contacts = 'т. офис (4852) 25-97-93, 42-97-77 E-mail: mng@727373.ru Web: www.tovaryplus.ru, www.727373.ru';
        $title = 'Отчет о запросах с сайта tovaryplus.ru';
        $firm_name = '';
        $firm_title = '';
        $type = 'Запросы с сайта tovaryplus.ru';
        $total_count = 0;

		//fonts
		$base_font = ['font' => ['name' => 'Arial Narrow', 'size' => '12', 'bold' => FALSE]];
        $small_font = ['font' => ['name' => 'Arial Narrow', 'size' => '8', 'bold' => FALSE]];
        $medium_font = ['font' => ['name' => 'Arial Narrow', 'size' => '10', 'bold' => FALSE]];
		$th_font = ['font' => ['name' => 'Arial Narrow', 'size' => '12', 'bold' => TRUE]];
		$h_font = ['font' => ['name' => 'Arial Narrow', 'size' => '14', 'bold' => FALSE]];
		$h_font_b = ['font' => ['name' => 'Arial Narrow', 'size' => '14', 'bold' => TRUE]];

		//pos
		$center = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP]];
		$center_center = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER]];
		$center_bottom = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM]];
		$right = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP]];
        

		$border_lined = ['borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]];

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial Narrow');
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(TRUE);
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
		$objPHPExcel->getProperties()->setCreator("TovaryPlus.ru")->setLastModifiedBy("TovaryPlus.ru")->setTitle($type)->setSubject($type)->setDescription($type);
		$sheet = $objPHPExcel->getActiveSheet();

		$bgColor = new PHPExcel_Style_Color();
		$bgColor->setRGB("C0C0C0");
		$sheet->getStyle("A6:D6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle("A6:D6")->getFill()->setStartColor($bgColor);

		$sheet->getDefaultRowDimension()->setRowHeight(15.75);
		$sheet->getRowDimension('1')->setRowHeight(30);
		$sheet->getRowDimension('3')->setRowHeight(30);

		$sheet->getStyle('A1')->applyFromArray($h_font)->applyFromArray($center_bottom);
		$sheet->setCellValue('A1', $our_title);
		$sheet->getStyle('A2')->applyFromArray($small_font)->applyFromArray($center);
        $sheet->setCellValue('A2', $our_contacts);
		$sheet->getStyle('A3')->applyFromArray($h_font)->applyFromArray($center_center);
        $sheet->setCellValue('A3', $title);
        
        if ($this->params['id_firm']) {
            $firm = new Firm($this->params['id_firm']);

            if ($firm->exists()) {
                $firm_name = $firm->name();
                $firm_title = 'Фирма ' . $firm->name();
            }
        }
        
		$sheet->getStyle('A4')->applyFromArray($th_font)->applyFromArray($center_center);
        $sheet->setCellValue('A4', $firm_title);

        $period = "Период: " . $this->params['t_start'] . " - " . $this->params['t_end'] . '. Дата отчета: ' . date("d.m.Y") . '.';
		$sheet->getStyle('A5')->applyFromArray($small_font)->applyFromArray($center);
        $sheet->setCellValue('A5', $period);

		$sheet->mergeCells('A1:D1');
		$sheet->mergeCells('A2:D2');
		$sheet->mergeCells('A3:D3');
		$sheet->mergeCells('A4:D4');
		$sheet->mergeCells('A5:D5');

		$sheet->getStyle("A6:D6")->applyFromArray($th_font);

		$sheet->setTitle($type);

		$sheet->getColumnDimension('A')->setWidth(4.57);
		$sheet->getColumnDimension('B')->setWidth(70);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);

		$sheet->setCellValue('A6', '№');
        $sheet->getStyle('A6')->applyFromArray($center);
		$sheet->setCellValue('B6', 'Наименование');
		$sheet->setCellValue('C6', 'Тип запроса');
        $sheet->getStyle('C6')->applyFromArray($center);
		$sheet->setCellValue('C6', 'Количество');
        $sheet->getStyle('C6')->applyFromArray($center);
		$sheet->setCellValue('D6', 'Сайт');
        $sheet->getStyle('D6')->applyFromArray($center);

		$i = 6;

        foreach ($data as $item) {
            $i++;
            $sheet->setCellValue('A'.$i, ($i - 6));
            $sheet->setCellValue('B'.$i, $item['name']);
            $sheet->setCellValue('C'.$i, $item['count']);
            $sheet->getStyle('C'.$i)->applyFromArray($center_center);
            $sheet->setCellValue('D'.$i, $item['site']);
            $sheet->getRowDimension($i)->setRowHeight(31.5);
            $total_count += (int)$item['count'];
        }
        $i++;
        
        $sheet->setCellValue('B'.$i, 'Итого по интернет');
        $sheet->getStyle('B'.$i)->applyFromArray($th_font);
        $sheet->setCellValue('C'.$i, $total_count);
        $sheet->getStyle('C'.$i)->applyFromArray($th_font)->applyFromArray($center);
        $i++;

        $sheet->setCellValue('B'.$i, 'ВСЕГО');
        $sheet->getStyle('B'.$i)->applyFromArray($th_font);
        $sheet->setCellValue('C'.$i, $total_count);
        $sheet->getStyle('C'.$i)->applyFromArray($th_font)->applyFromArray($center);

        $sheet->getStyle('A6:D'.$i)->applyFromArray($border_lined);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.2);
        $sheet->getPageMargins()->setLeft(0.2);
        $sheet->getPageMargins()->setBottom(0.2);

        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $sheet->getHeaderFooter()->setDifferentOddEven(false);
        $sheet->getHeaderFooter()->setOddHeader('&R&8Информационный центр "Товары плюс"');
        $sheet->getHeaderFooter()->setOddFooter('&R&8Спрос на товары и услуги: ' . $firm_name . '. Страница &P из &N.');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$type.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter->save('php://output');
		exit();
	}

}
