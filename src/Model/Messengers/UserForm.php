<?php

namespace App\Model\Messengers;

use App\Model\Firm;

class UserForm extends \Sky4\Model\Form {

    private $firm;

    public function __construct(Model $model = null, $firm = null) {
        if (!($this->model() instanceof Firm)) {
            $this->setModel(new Firm());
        }
        $this->firm = $firm;
        parent::__construct($model, []);
    }

    public function controls() {
        return [
            'submit' => [
                'elem' => 'button',
                'label' => 'Сохранить',
                'attrs' => [
                    'class' => 'send js-send btn btn_primary'
                ]
            ]
        ];
    }

    public function attrs() {
        return [
            'accept-charset' => 'utf-8',
            'action' => '/firm-user/submit/messengers/?redirect=/firm-user/info/?mode=messengers&success',
            'method' => 'post'
        ];
    }

    public function editableFieldsNames() {
        return [
            'company_vk',
            'company_fb',
            'company_in',
            'company_viber',
            'company_whatsapp',
            'company_skype',
            'company_telegram'
        ];
    }

    public function fields() {
        return [
            'id_firm' => [
                'elem' => 'hidden_field'
            ],
            'company_viber' => [
                'elem' => 'text_field',
                'label' => 'Viber <span style="font-size:10px;font-weight:bold;">(от 6 до 32 символов, только цифры)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 32, 'min' => 6)]
                ],
                'attrs' => [
                    'class' => 'js-only-numbers'
                ]
            ],
            'company_whatsapp' => [
                'elem' => 'text_field',
                'label' => 'WhatsApp <span style="font-size:10px;font-weight:bold;">(от 6 до 32 символов, только цифры)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 32, 'min' => 6)]
                ],
                'attrs' => [
                    'class' => 'js-only-numbers'
                ]
            ],
            'company_skype' => [
                'elem' => 'text_field',
                'label' => 'Skype <span style="font-size:10px;font-weight:bold;">(от 6 до 32 символов, только цифры, латинские буквы(в любом регистре) и символы _ - . ,)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 32, 'min' => 6)]
                ]
            ],
            'company_telegram' => [
                'elem' => 'text_field',
                'label' => 'Telegram <span style="font-size:10px;font-weight:bold;">(только цифры, латинские буквы(в любом регистре) и символ _)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 128, 'min' => 1)]
                ]
            ],
            'company_vk' => [
                'elem' => 'text_field',
                'label' => 'Вконтакте <span style="font-size:10px;font-weight:bold;">(например https://vk.com/company/)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 128, 'min' => 1)]
                ]
            ],
            'company_fb' => [
                'elem' => 'text_field',
                'label' => 'Facebook <span style="font-size:10px;font-weight:bold;">(например https://www.facebook.com/company/)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 128, 'min' => 1)]
                ]
            ],
            'company_in' => [
                'elem' => 'text_field',
                'label' => 'Instagram <span style="font-size:10px;font-weight:bold;">(например https://www.instagram.com/company/)</span>',
                'params' => [
                    'rules' => ['length' => array('max' => 128, 'min' => 1)]
                ]
            ],
        ];
    }

    // -------------------------------------------------------------------------

    public function render(Firm $firm) {
        $errors = [
            1 => 'В поле Вконтакте: введен некорректный url',
            2 => 'В поле Facebook: введен некорректный url',
            3 => 'В поле Instagram: введен некорректный url',
            4 => 'В поле Viber: от 6 до 32 символов, только цифры',
            5 => 'В поле Whatsapp: от 6 до 32 символов, только цифры',
            6 => 'В поле Skype: от 6 до 32 символов, только цифры, латинские буквы(в любом регистре) и символы _ - . ,',
            7 => 'В поле Telegram: только цифры, латинские буквы(в любом регистре) и символ _',
        ];

        $error_code = app()->request()->processGetParams([
                    'error_code' => ['type' => 'int']
                ])['error_code'];

        if ($error_code !== null && $error_code) {
            $this->errorHandler()->setError('submit',$errors[$error_code], $error_code);
        }

        return $this->view()
                        ->set('errors', $this->errorHandler()->getErrors())
                        ->set('attrs', $this->getAttrs())
                        ->set('controls', $this->renderControls())
                        ->set('fields', $this->renderFields())
                        ->set('firm', $firm)
                        ->setTemplate('firm_messengers', 'forms')
                        ->render();
    }

}
