<?php

namespace App\Action\Crontab;

class SyncUser extends \App\Action\Crontab {

	public function __construct() {
		parent::__construct();
		$this->file_name = 'sts_users.txt';
	}

	public function execute() {
		$result = $this->startAction()
				->sync();
		$this->endAction();

		return $result;
	}

	protected function sync() {
        $this->file_name = 'sts_users.txt';
        $dump_file = new \Sky4\FileSystem\File($this->dir().'/'.$this->fileName());

        $i = 0;
        if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
			$this->log('Синхронизация User');
            
            while ($data = fgetcsv($handle, 50000, "	")) {
                list($id_user, $name, $login, $password, $parent_node, $default_email) = $data;
                $row = [
                    'id_user' => (int) $id_user,
                    'name' => $name,
                    'login' => $login,
                    'parent_node' => (int) $parent_node,
                    'email_default' => $default_email
                ];

                if ((int) $id_user) {
                    $firm_manager = new FirmManager();
                    $firm_manager->setWhere(['AND', 'id_user = :id_user'], [':id_user' => (int) $id_user])
                            ->getByConds();

                    if ($firm_manager->exists()) {
                        $firm_manager->update($row);
                    } else {
                        $firm_manager->insert($row);
                    }
                }
                $i++;
            }
            fclose($handle);
        }

        $this->log('обработано записей: ' . $i);
        $this->log('Синхронизация завершена');

        return $this;
	}

}
