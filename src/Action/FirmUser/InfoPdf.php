<?php

namespace App\Action\FirmUser;
require_once APP_DIR_PATH . '/protected/mpdf/src/functions.php';

use App\Action\FirmUser as FirmUserAction;
use App\Model\FirmContract;
use App\Model\FirmDescription;
use App\Model\FirmManager;
use App\Model\FirmUser;
use mPDF;
use const APP_DIR_PATH;
use const APP_URL;
use function app;

class InfoPdf extends FirmUserAction {

	public function execute() {
		$description = new FirmDescription();
		$description->getByIdFirm($this->firm()->id());
		$_branches = $this->firm()->getBranches();
		$branches = [];
		foreach ($_branches as $br) {
			$branches[] = $br->name();
		}
		$contract = new FirmContract();
		$contract->getByIdFirm(app()->firmUser()->id_firm());

		$user = new FirmUser();
		$user->getByIdFirm(app()->firmUser()->id_firm());

		$manager = new FirmManager();
		$manager->getByFirm($this->firm);

		$data = [
			'Название фирмы для БД' => $this->firm()->val('company_name_ratiss'),
			'Юридическое название' => $this->firm()->val('company_name_jure'),
			'Название фирмы для сайта' => $this->firm()->name(),
			'Адрес' => $this->firm()->address(),
			'Телефон' => $this->firm()->phone(),
			'Факс' => $this->firm()->hasFax() ? $this->firm()->fax() : '',
			'Телефон для переадресации' => $this->firm()->val('company_phone_readdress'),
			'Телефон для смс' => $this->firm()->cellPhone(),
			'Сайт' => implode(', ', $this->firm()->webSiteUrls()),
			'Email' => implode(', ', $this->firm()->emailAddresses()),
            'VK' => $this->firm()->val('company_vk'),
            'Facebook' => $this->firm()->val('company_fb'),
            'Instagram' => $this->firm()->val('company_in'),
            'Viber' => $this->firm()->val('company_viber'),
            'WhatsApp' => $this->firm()->val('company_whatsapp'),
            'Skype' => $this->firm()->val('company_skype'),
            'Telegram' => $this->firm()->val('company_telegram'),
			'Вид деятельности' => $this->firm()->activity(),
			'Фирма производитель' => (int) $this->firm()->val('flag_is_producer') === 1 ? 'да' : 'нет',
			'Режим работы' => $this->firm()->modeWork(),
			'Проезд' => $this->firm()->path(),
			'Прикрепленное описание' => $description->exists() ? $description->val('text') : 'нет',
			'Филиалы фирмы' => implode(', ', $branches),
			'Договор' => $contract->name(),
			'Email для входа в ЛК' => $user->val('email') . ' <a href="' . APP_URL . '/#login">вход в личный кабинет</a>'
		];
        
        $firm_branches = [];
        $_fb = $this->firm()->getFirmBranches();
        if ($_fb) {
            foreach($_fb as $id_city => $items) {
                foreach ($items as $item) {
                    $firm_branches []= [ 
                        'item' => $item, 
                        'data' => [
                            'Название Филиала для сайта' => $item->name(),
                            'Юридическое название' => $item->val('company_name_jure'),
                            'Адрес' => $item->address(),
                            'Телефон' => $item->phone(),
                            'Факс' => $item->hasFax() ? $item->fax() : '',
                            'Сайт' => implode(', ', $item->webSiteUrls()),
                            'Email' => implode(', ', $item->emailAddresses()),
                            'VK' => $item->val('company_vk'),
                            'Facebook' => $item->val('company_fb'),
                            'Instagram' => $item->val('company_in'),
                            'Viber' => $item->val('company_viber'),
                            'WhatsApp' => $item->val('company_whatsapp'),
                            'Skype' => $item->val('company_skype'),
                            'Telegram' => $item->val('company_telegram'),
                            'Вид деятельности' => $item->activity(),
                            'Режим работы' => $item->modeWork(),
                            'Проезд' => $item->path(),
                            'Дополнительная информация' => $item->val('text'),
                            'Прикреплен прайс фирмы' => $item->val('flag_is_price_attached') ? 'Да' : 'Нет'
                        ]
                    ];
                }
            }
        }

		$html = $this->view()
				->set('data', $data)
				->set('firm', $this->firm)
				->set('firm_branches', $firm_branches)
				->set('description', $this->firm()->hasDescription() ? $this->firm()->description() : '')
				->set('manager', $manager->exists() ? $manager : app()->firmManager())
				->setTemplate('info_index_pdf')
				->render();

		if ($this->isHtmlMode()) {
			return $html;
		}

		$mpdf = new \Mpdf\Mpdf(['utf-8', 'A4', '10', 'Arial', 10, 10, 7, 7, 10, 10]);
		$stylesheet = file_get_contents(APP_DIR_PATH . '/public/css/pdf.css');
		$mpdf->WriteHTML($stylesheet, 1);

		$mpdf->list_indent_first_level = 0;
		$mpdf->WriteHTML($html, 2); /* формируем pdf */
		$mpdf->Output('карточка_фирмы.pdf', 'I');
		exit();
	}

}
