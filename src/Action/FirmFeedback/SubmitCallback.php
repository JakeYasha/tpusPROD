<?php

namespace App\Action\FirmFeedback;

use App\Action\FirmFeedback;
use App\Model\Firm;
use App\Model\FirmManager;
use function app;

class SubmitCallback extends FirmFeedback {

    public function execute() {
        $result_type = 'json';
        $params = app()->request()->processPostParams([
            'id_firm' => ['type' => 'int'],
            'id_service' => ['type' => 'int'],
            'message_text' => ['type' => 'string'],
            'user_name' => ['type' => 'string'],
            'user_phone' => ['type' => 'string'],
            'flag_is_for_call_center' => ['type' => 'int']
        ]);

        if ($params['flag_is_for_call_center'] === null) {
            $params['flag_is_for_call_center'] = 0;
        }

        if ($params['flag_is_for_call_center'] != 0) {
            $result_type = 'plain';
        }


        $referer = app()->request()->processPostParams([
                    'referer' => ['type' => 'string']
                ])['referer'];

        $cap = app()->request()->processPostParams([
            'g-recaptcha-response' => ['type' => 'string']
        ]);

        if (app()->capcha()->isValid($cap['g-recaptcha-response']) || $params['flag_is_for_call_center']) {
            $params['flag_is_callback'] = 1;
            if ($referer) {
                $params['message_text'] = $referer;
            }
            
            $id_firm = $params['id_firm'];
            $id_service = isset($params['id_service']) && $params['id_service'] ? $params['id_service'] : null;

            $firm = new Firm();
            if ($id_service === null) {
                $firm->getByIdFirm((int) $id_firm);
            } else {
                $firm->getByIdFirmAndIdService($id_firm, $id_service);
            }

            unset($params['id_service']);
            $params['id_firm'] = $firm->id();
            
            if ($this->model()->insert($params)) {
                //app()->stat()->addObject(StatObject::FORM_CALLBACK_SEND, $this->model())
                //		->fixResponse(false);
                if ($firm->exists() && $firm->hasCellPhone() && $firm->id_service() === 10) {
                    app()->email()
                            ->setParams(['name' => $params['user_name'], 'phone' => $params['user_phone']])
                            ->setSubject('!SMS заказ обратного звонка')
                            ->setTemplate('sms_callback_to_firm', 'firm')
                            ->sendSmsToQuery($firm->val('company_cell_phone'));

                    if ($firm->id_service() === 10 && $firm->id_manager() !== null) {
                        $firm_manager = new FirmManager();
                        $firm_manager->getByFirm($firm);

                        if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
                            if ($firm_manager->val('email') !== '') {
                                app()->email()
                                        ->setSubject('Новый заказ звонка для фирмы ' . $firm->name())
                                        ->setTo($firm_manager->val('email'))
                                        ->setParams(['name' => $params['user_name'], 'phone' => $params['user_phone'], 'message_text' => $params['message_text']])
                                        ->setModel($firm)
                                        ->setTemplate('email_callback_to_manager', 'firm')
                                        ->sendToQuery();
                            } else {
                                app()->email()
                                        ->setSubject('Новый заказ звонка для фирмы ' . $firm->name())
                                        ->setTo($firm_manager->val('email_default'))
                                        ->setParams(['name' => $params['user_name'], 'phone' => $params['user_phone'], 'message_text' => $params['message_text']])
                                        ->setModel($firm)
                                        ->setTemplate('email_callback_to_manager', 'firm')
                                        ->sendToQuery();
                            }
                        }
                    }

                    $result = ['ok' => true, 'html' => app()->chunk()->setArg(app()->config()->get('forms.firm.callback.success', 'ok'))->render('common.default_ajax_form_message')];
                    return $this->renderResult($result, $result_type, 'success');
                } else {
                    $result = ['error_code' => 0, 'error_message' => app()->config()->get('forms.firm.callback.fail', 'error')];
                    return $this->renderResult($result, $result_type, 'fail');
                }
            } else {
                $result = ['error_code' => 0, 'error_message' => app()->config()->get('forms.firm.callback.fail', 'error')];
                return $this->renderResult($result, $result_type, 'fail');
            }
        } else {
            $result = ['error_code' => 0, 'error_message' => 'Вы робот?'];
            return $this->renderResult($result, $result_type, 'fail');
        }

        //die(json_encode($result));
    }

}
