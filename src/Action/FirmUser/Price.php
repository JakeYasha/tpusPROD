<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Classes\PHPExcel;
use App\Controller\Firm;
use App\Model\FirmContract;
use App\Model\FirmManager;
use App\Model\PriceCatalog;
use App\Presenter\PriceItems;
use App\Classes\PHPExcel\PHPExcel_IOFactory;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Alignment;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Border;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Color;
use App\Classes\PHPExcel\Style\PHPExcel_Style_Fill;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Widget\InterfaceElem\Autocomplete;
use function app;

class Price extends FirmUser {

	public function __construct() {
		parent::__construct();
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Прайс-лист фирмы', '/firm-user/price/');
	}

	public function execute() {
		app()->startTimer();
		$this->params = app()->request()->processGetParams([
			'id_catalog' => ['type' => 'int'],
			'q' => ['type' => 'string'],
			'sorting' => ['type' => 'string'],
			'display_mode' => ['type' => 'string'],
			'export' => ['type' => 'string'],
			'id_firm' => ['type' => 'int'] //remove it
		]);
		app()->metadata()->setTitle('Личный кабинет - прайс-лист');
		if ($this->params['id_firm']) {
			$this->firm = new \App\Model\Firm($this->params['id_firm']);
		}


		$price_presenter = new PriceItems();
		$price_presenter->setItemsTemplateSubdirName('firmuser');
		if ($this->params['export'] === 'xls') {
			$price_presenter->setLimit(5000);
		} else {
			$price_presenter->setLimit(20);
		}

		$price_presenter->findAllInFirm($this->firm, $this->params, true);

		if ($this->params['q'] === null) {
			$catalog = new PriceCatalog($this->params['id_catalog']);
			$path = $catalog->adjacencyListComponent()->getPath();
			foreach ($path as $cat) {
				if ($cat->node_level() >= 2) {
					app()->breadCrumbs()
							->setElem($cat->name(), app()->linkFilter('/firm-user/price/', $this->params));
				}
			}
		} else {
			$catalog = new PriceCatalog();
			app()->breadCrumbs()
					->setElem('Поиск', '');
		}

		$autocomplete = new Autocomplete();
		$attrs = ['id' => 'price-search-autocomplete', 'placeholder' => 'Поиск по прайс-листу фирмы'];
		if ($this->params['q'] !== null) {
			$attrs['value'] = (string)urldecode($this->params['q']);
		}
		$autocomplete
				->setName('q')
				->setAttrs($attrs)
				->setParams([
					'model_alias' => 'price-user-search',
					'val_mode' => 'id',
					'field_name' => $this->firm()->id()
		]);

		if ($this->params['export'] === 'xls') {
			$this->exportPriceList($price_presenter->getItems());
			exit();
		}

		/* return */$this->view()
				->set('autocomplete', $autocomplete->render())
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('catalog', $catalog)
				->set('items', $price_presenter->renderItems())
				->set('pagination', $price_presenter->pagination()->render(true))
				->set('total_founded', $price_presenter->pagination()->getTotalRecords())
				->set('total_price_list_count', $this->firm()->getTotalPriceListCount())
				->set('tabs', app()->tabs()->setDisplayMode(false)->render(true))
				->set('tags', Firm::renderTagsByFirm($this->firm, $this->params, 'firmuser.catalog_tags_chunk'))
				//->set('inside_bread_crumbs', app()->breadCrumbs()->renderBottom())
				->set('sorting', app()->tabs()
						->setDisplayMode(false)
						->setActiveSortOption($this->params['sorting'])
						->renderSorting(true))
				->set('url', '/firm-user/price/')
				->set('filters', $this->params)
				->setTemplate('price_index')
				->save();
	}

	private function exportPriceList($data) {
		//$this->auth()->setFirm();

		$contract = new FirmContract();
		$contract->getByFirm($this->firm);

		$manager = new FirmManager();
		$manager->getByFirm($this->firm);

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
		$objPHPExcel->getProperties()->setCreator("TovaryPlus.ru")->setLastModifiedBy("TovaryPlus.ru")->setTitle("Прайс-лист")->setSubject("Прайс-лист")->setDescription("Прайс-лист");
		$sheet = $objPHPExcel->getActiveSheet();

		$bgColor = new PHPExcel_Style_Color();
		$bgColor->setRGB("C0C0C0");
		$sheet->getStyle("A11:O11")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle("A11:O11")->getFill()->setStartColor($bgColor);

		$sheet->getDefaultRowDimension()->setRowHeight(12.75);
		$sheet->getRowDimension('2')->setRowHeight(19.50);

		$sheet->getStyle('A1')->applyFromArray($base_font)->applyFromArray($right);
		$sheet->setCellValue('A1', 'Товары плюс '.date("d.m.Y"));
		$sheet->getStyle('A2')->applyFromArray($h_font)->applyFromArray($center);

		$sheet->mergeCells('A1:O1');
		$sheet->mergeCells('A2:O2');

		$sheet->setCellValue('D3', "Фирма: ".$this->firm()->name());
		$sheet->setCellValue('D4', "Адрес: ".$this->firm()->address());
		$sheet->setCellValue('D5', "Тел.: ".$this->firm()->phone());
		$sheet->setCellValue('D6', "Fax: ".$this->firm()->fax());
		$sheet->setCellValue('D7', "E-mail: ".$this->firm()->email());
		$sheet->setCellValue('D8', "Web: ".$this->firm()->webSiteMain());
		$sheet->setCellValue('D9', 'Код договора: '.$contract->name().'. Дата договора: c '.date('d.m.Y', DeprecatedDateTime::toTimestamp($contract->val('timestamp_beginning'))).' по '.date('d.m.Y', DeprecatedDateTime::toTimestamp($contract->val('timestamp_ending'))));
		$sheet->setCellValue('D10', "Менеджер: ".$manager->name());

		$sheet->mergeCells('D3:L3');
		$sheet->mergeCells('D4:L4');
		$sheet->mergeCells('D5:L5');
		$sheet->mergeCells('D6:L6');
		$sheet->mergeCells('D7:L7');
		$sheet->mergeCells('D8:L8');
		$sheet->mergeCells('D9:O9');
		$sheet->mergeCells('D10:O10');

		$sheet->getStyle('N3')->applyFromArray($base_font)->applyFromArray($right);
		$sheet->setCellValue('N3', "Легенда:");
		$sheet->setCellValue('O3', "Ж - строка в журнале Дело");
		$sheet->setCellValue('O4', "И - строка в интернет");
		$sheet->setCellValue('O5', "С - строка в справочной службе");
		$sheet->setCellValue('O6', "Опис - наличие описания");
		$sheet->setCellValue('O7', "Изоб - наличие изображения");
		$sheet->setCellValue('O8', "% - наличие скидки");

		$sheet->getStyle("A11:O11")->applyFromArray($th_fonr);

		$sheet->setCellValue('A2', 'Прайс-лист фирмы');
		$sheet->setTitle('Прайс-лист');

		$sheet->getColumnDimension('A')->setWidth(4.57);
		$sheet->getColumnDimension('B')->setWidth(3);
		$sheet->getColumnDimension('C')->setWidth(3);
		$sheet->getColumnDimension('D')->setWidth(3);
		$sheet->getColumnDimension('E')->setWidth(6);
		$sheet->getColumnDimension('F')->setWidth(6);
		$sheet->getColumnDimension('G')->setWidth(6);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(22);
		$sheet->getColumnDimension('J')->setWidth(8.14);
		$sheet->getColumnDimension('K')->setWidth(10.86);
		$sheet->getColumnDimension('L')->setWidth(16);
		$sheet->getColumnDimension('M')->setWidth(7.29);
		$sheet->getColumnDimension('N')->setWidth(12.71);
		$sheet->getColumnDimension('O')->setWidth(35);

		$sheet->setCellValue('A11', '№');
		$sheet->setCellValue('B11', 'Ж');
		$sheet->setCellValue('C11', 'И');
		$sheet->setCellValue('D11', 'С');
		$sheet->setCellValue('E11', 'Опис.');
		$sheet->setCellValue('F11', 'Изоб.');
		$sheet->setCellValue('G11', '%');
		$sheet->setCellValue('H11', 'Наименование');
		$sheet->setCellValue('I11', 'Производство');
		$sheet->setCellValue('J11', 'Ед.из.');
		$sheet->setCellValue('K11', 'Фасовка');
		$sheet->setCellValue('L11', 'Форма оплаты');
		$sheet->setCellValue('M11', 'Цена');
		$sheet->setCellValue('N11', 'Дата');
		$sheet->setCellValue('O11', 'Расширенная информация');

		$i = 11;

		$currencies = [];
		$pricess = [];
		foreach ($data as $item) {
			$i++;
			$currencies = $pricess = array();
			if ($item['price_retail'] || $item['price']) {
				$currencies[] = 'розница';
				$pricess[] = $item['price'];
			}
			if ($item['price_wholesale']) {
				$currencies[] = 'опт';
				$pricess[] = $item['price_wholesale'];
			}
			$sheet->setCellValue('A'.$i, ($i - 11));
			$sheet->setCellValue('B'.$i, '+');
			$sheet->setCellValue('C'.$i, '+');
			$sheet->setCellValue('D'.$i, '+');
			$sheet->setCellValue('E'.$i, $item['info'] ? '+' : '');
			$sheet->setCellValue('F'.$i, $item['image_id'] ? '+' : '');
			$sheet->setCellValue('G'.$i, $item['old_price'] ? '+' : '-');
			$sheet->setCellValue('H'.$i, $item['name']);
			$sheet->setCellValue('I'.$i, $item['production']);
			$sheet->setCellValue('J'.$i, mb_strtolower($item['unit'], 'UTF-8'));
			$sheet->setCellValue('K'.$i, $item['pack']);
			$sheet->setCellValue('L'.$i, isset($currencies) ? implode(", ", $currencies) : '');
			$sheet->setCellValue('M'.$i, isset($pricess) ? implode(", ", $pricess) : '');
			$sheet->setCellValue('N'.$i, date("d.m.Y", DeprecatedDateTime::toTimestamp($item['datetime'])));
			$sheet->setCellValue('O'.$i, strip_tags(trim(str_replace("\\", " ", $item['info']))));
			$sheet->getRowDimension($i)->setRowHeight(31.5);
		}

		$sheet->setCellValue('A'.$i += 2, 'Режим работы');
		$sheet->getStyle('A'.$i)->applyFromArray($center)->applyFromArray($th_fonr);
		$sheet->mergeCells('A'.$i.':O'.$i);
		$sheet->getStyle("A$i:O$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle("A$i:O$i")->getFill()->setStartColor($bgColor);
		$sheet->setCellValue('A'.++$i, $item['firm']->modeWork());
		$sheet->mergeCells('A'.$i.':O'.$i);
		$sheet->getRowDimension($i)->setRowHeight(20.5);

		$sheet->setCellValue('A'.$i += 2, 'Как проехать');
		$sheet->getStyle('A'.$i)->applyFromArray($center)->applyFromArray($th_fonr);
		$sheet->mergeCells('A'.$i.':O'.$i);
		$sheet->getStyle("A$i:O$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle("A$i:O$i")->getFill()->setStartColor($bgColor);
		$sheet->setCellValue('A'.++$i, $item['firm']->path());
		$sheet->mergeCells('A'.$i.':O'.$i);
		$sheet->getRowDimension($i)->setRowHeight(20.5);

		$sheet->setCellValue('A'.$i += 2, 'Дополнительная к фирме');
		$sheet->getStyle('A'.$i)->applyFromArray($center)->applyFromArray($th_fonr);
		$sheet->mergeCells('A'.$i.':O'.$i);
		$sheet->getStyle("A$i:O$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle("A$i:O$i")->getFill()->setStartColor($bgColor);
		$sheet->setCellValue('A'.++$i, strip_tags($item['firm']->about()));
		$sheet->mergeCells('A'.$i.':O'.$i);
		$sheet->getRowDimension($i)->setRowHeight(20.5);

		$sheet->getStyle('A12:O'.$i)->applyFromArray($border_dashed);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.'Прайс-лист'.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter->save('php://output');
		exit();
	}

}
