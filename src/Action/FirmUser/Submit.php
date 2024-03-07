<?php

namespace App\Action\FirmUser;

use App\Model\DraftFirm;
use App\Model\DraftFirm\UserForm as DraftFirmUserForm;
use App\Model\Messengers\UserForm as MessengersUserForm;
use App\Model\Image;
use App\Model\StsService;
use Sky4\Utils;
use function app;

class Submit extends \App\Action\FirmUser {

    public function execute($form_alias) {
        $redirect = app()->request()->processGetParams(['redirect' => ['type' => 'string']])['redirect'];

        $form = Utils::getFormClass($form_alias, 'User');
        $form->setInputVals($_POST);
        $errors = $form->errorHandler()->getErrors();

        if (!$errors) {
            if ($form instanceof DraftFirmUserForm) {
                $df = new DraftFirm();
                $df->getByFirm($this->firm());
                $df->delete();

                $base_vals = $this->firm()->getVals();
                $form_vals = $form->getVals();

                if (isset($form_vals['file_logo']) && $base_vals['file_logo'] !== $form_vals['file_logo']) {
                    $image = new Image($form_vals['file_logo']);
                    $image->update(['source' => 'temp']);
                    $form_vals['file_logo'] = $image->embededFileComponent()->setSubDirName('image')->iconLink('-160');
                }

                $form->setVals($base_vals);
                $form->setVals($form_vals);
                $form->model()->resetId();

                $redirect = '/firm-user/info/?success';
            } elseif ($form instanceof \App\Model\FirmPromo\UserForm) {
                $form_vals = $form->getVals();
                if ($form_vals['flag_is_infinite']) {
                    $form_vals['timestamp_ending'] = date('Y-m-d', strtotime('+1 year', strtotime($form_vals['timestamp_beginning'])));
                    $form->setVals($form_vals);
                }
                $form->model()->setVals($form->getVals());
            } elseif ($form instanceof \App\Model\AdvertModule\UserForm) {
                $form_vals = $form->getVals();
                $form_vals['name'] = $form_vals['header'];
                $manager = app()->firmManager();
                if (empty($form_vals['region_ids']) && $manager && $manager->val('id_service')) {
                    $service = new StsService($manager->val('id_service'));
                    if ($service) {
                        $form_vals['region_ids'] = !empty($form_vals['region_ids']) ? $form_vals['region_ids'] : $service->val('id_region_country');
                    }
                }
                $form_vals['timestamp_beginning'] = date('Y-m-d 03:00:00', strtotime($form_vals['timestamp_beginning']));
                $form_vals['timestamp_ending'] = date('Y-m-d 23:59:59', strtotime($form_vals['timestamp_ending']));

                $form->setVals($form_vals);
                $form->model()->setVals($form->getVals());
            } elseif ($form instanceof MessengersUserForm) {
                $form_vals = $form->getVals();

                $error_code = 0;
                $form_vals['company_vk'] = trim($form_vals['company_vk']);
                $form_vals['company_fb'] = trim($form_vals['company_fb']);
                $form_vals['company_in'] = trim($form_vals['company_in']);
                $form_vals['company_viber'] = trim($form_vals['company_viber']);
                $form_vals['company_whatsapp'] = trim($form_vals['company_whatsapp']);
                $form_vals['company_skype'] = trim($form_vals['company_skype']);
                $form_vals['company_telegram'] = trim($form_vals['company_telegram']);
                if ($form_vals['company_telegram'] && preg_match('/^[0-9A-Za-z_]+$/', $form_vals['company_telegram']) !== 1) {
                    $error_code = 7;
                }
                if ($form_vals['company_skype'] && preg_match('/^[A-Za-z\d\,\-\.\_]{6,32}$/', $form_vals['company_skype']) !== 1) {
                    $error_code = 6;
                }
                if ($form_vals['company_whatsapp'] && preg_match('/^[0-9]{6,32}$/', $form_vals['company_whatsapp']) !== 1) {
                    $error_code = 5;
                }
                if ($form_vals['company_viber'] && preg_match('/^[0-9]{6,32}$/', $form_vals['company_viber']) !== 1) {
                    $error_code = 4;
                }
                if ($form_vals['company_in'] && filter_var($form_vals['company_in'], FILTER_VALIDATE_URL) === FALSE) {
                    $error_code = 3;
                }
                if ($form_vals['company_fb'] && filter_var($form_vals['company_fb'], FILTER_VALIDATE_URL) === FALSE) {
                    $error_code = 2;
                }
                if ($form_vals['company_vk'] && filter_var($form_vals['company_vk'], FILTER_VALIDATE_URL) === FALSE) {
                    $error_code = 1;
                }
                

                if ($error_code !== 0) {
                    app()->response()->redirect(app()->linkFilter('/firm-user/info/?mode=messengers', ['mode' => 'messengers', 'error_code' => $error_code]));
                } else {
                    $redirect = app()->linkFilter('/firm-user/info/?mode=messengers', ['mode' => 'messengers', 'success' => 1]);
                }

                $form->model()->reader()->object((int) $this->firm()->id());
                unset($form_vals['id_firm']);
                $form->model()->setVals($form_vals);
            } else {
                $form->model()->setVals($form->getVals());
            }

            if ($form->model()->exists()) {
                $form->model()->update($form->getVals());
            } else {
                $form->model()->insert($form->getVals());
            }
        }

        app()->response()->redirect($redirect);
    }

}
