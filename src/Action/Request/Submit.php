<?php

namespace App\Action\Request;

class Submit extends \App\Action\Request {

	public function execute() {
		
		app()->log('### Submit go"');
		$params = app()->request()->processPostParams([
			'g-recaptcha-response' => ['type' => 'string']
		]);
		$form = new \App\Model\Request\FormAdd($this->model());
		//files_block
		$files_result = [];
		foreach ($_FILES as $k => $file) {
			if ($file['error']) continue;

			$uploader = new \Sky4\Component\DeprecatedFileUploader();
			$uploader
					->setFileDirPath(APP_DIR_PATH . '/public/uploaded/')
					->setUseAutoSubdirs(true)
					->setVarName($k)
					->processFile()
					->uploadFile($file);

			$data = $uploader->getFileData();
			$result = [];

			foreach ($data as $kk => $vv) {
				$result['file_' . $kk] = $vv;
			}

			$result['file_subdir_name'] = implode('/', $result['file_subdirs_names']);
			unset($result['file_subdirs_names']);
			unset($result['file_dir_path']);

			$f = new \App\Model\File();
			$f->insert($result);

			$files_result[] = '<a href="' . str()->replace($f->embededFileComponent()->path(), APP_DIR_PATH . '/public/', '') . '">' . $f->val('file_raw_name') . '</a>';
		}

		$_POST['files'] = implode('<br/>', $files_result);
		//

		$form->setInputVals($_POST);

		if (!$form->validate()) {
			$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
		} else if (!app()->capcha()->isValid($params['g-recaptcha-response'])) {
			$form->errorHandler()->setError('', 'Вы робот?');
		} else if (($form->getVals() === null) || !is_array($form->getVals())) {
			$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
		} else if (!$this->model()->insert($form->getVals())) {
			$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
		}

		if ($form->errorHandler()->hasErrors()) {
			$form->errorHandler()->saveErrorsInSession()
					->saveValsInSession($form->getVals());
			app()->response()->redirect('/request/result/fail/');
		}

		$email = app()->config()->get('app.email.manager');
		if ($this->model()->val('id_region_country')) {
			$service = new \App\Model\StsService();
			$service->reader()
					->setWhere(['AND', 'id_region_country = :id_region_country', 'exist = :exist'], [':id_region_country' => (int) $this->model()->val('id_region_country'), ':exist' => 1])
					->objectByConds();
			if ($service->exists() && $service->val('email')) {
				$email = $service->val('email');
			}
		}

		app()->email()
				->setSubject('Новый запрос на договор')
				->setTo($email)
				->setModel($this->model())
				->setTemplate('email_to_admin', 'request')
				->sendToQuery();

		app()->response()->redirect('/request/result/success/');
	}

}
