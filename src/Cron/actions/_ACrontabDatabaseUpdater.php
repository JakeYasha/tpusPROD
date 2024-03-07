<?php

class ACrontabDatabaseUpdater extends ACrontabAction {

    public function run() {
        $this
                ->log('старт обновления базы из /app/cron/update/utf8/')
                ->deleteStsCost()
                ->deleteStsFirm()
                ->deleteStsPrice()
                ->loadData('sts_city_type')
                ->loadData('sts_cost')
                ->loadData('sts_country')
                ->loadData('sts_currency')
                ->loadData('sts_email_client')
                ->loadData('sts_groups')
                ->loadData('sts_subgroup')
                ->loadData('sts_producer_country')
                ->loadData('sts_region_city')
                ->loadData('sts_region_country')
                ->loadData('sts_subgroup')
                ->loadData('sts_hist_answer')
                ->loadData('sts_hist_calls')
                ->loadData('sts_hist_export_detail')
                ->loadData('sts_hist_readdress')
                ->storeStsUsers()
                ->storeStsCity()
                ->storeStsPrice()
                ->storeStsFirm()
                ->reindex();

        return parent::run();
    }

    private function reindex() {
        $this->log('переиндексация товаров и фирм');
        App::system()->reindex(SPHINX_FIRM_INDEX);
        App::system()->reindex(SPHINX_PRICE_INDEX);
        return $this;
    }

    private function storeStsCity() {
        $table_name = 'ratiss_city';
        $this
                ->log('обновление таблицы sts_city')
                ->loadData('sts_city', $table_name);

        $cities = $this->db->query()
                ->setFrom($table_name)
                ->setWhere(['AND', 'id_city != :id_city'], [':id_city' => 0])
                ->select();

        foreach ($cities as $city) {
            $sc = new StsCity();
            $sc->setWhere(['AND', 'id_city = :id_city'], [':id_city' => $city['id_city']])
                    ->getByConds();

            if (!$sc->exists()) {
                $city['position_weight'] = 0;
                $sc->insert($city);
            } else {
                $sc->update($city);
            }
        }

        return $this;
    }

    private function storeStsFirm() {
        $table_name = 'ratiss_firm';
        App::db()->query()->truncateTable($table_name);

        $this
                ->log('обновление таблицы ' . $table_name)
                ->loadData('sts_firm', $table_name);

        $firms = $this->db->query()
                ->setFrom($table_name)
                ->setWhere('`id_service` != :nil', [':nil' => 0])
                ->select();

        foreach ($firms as $ob) {
            $firm_where = ['AND', '`id_firm` = :id_firm', 'id_service = :id_service'];
            $firm_params = [':id_firm' => $ob['id_firm'], ':id_service' => $ob['id_service']];

            $contract = new FirmContract();
            $contract->setWhere($firm_where, $firm_params)
                    ->getByConds();
            if (!$contract->exists()) {
                if ($ob['dog_number']) {
                    $contract->insert([
                        'name' => $ob['dog_number'] ? $ob['dog_number'] : '',
                        'timestamp_beginning' => $ob['dog_begin_date'],
                        'timestamp_ending' => $ob['dog_end_date']
                    ]);
                }
            }
            $contract_id = $contract->id();

            $user = new FirmUser();
            $user->setWhere($firm_where, $firm_params)
                    ->getByConds();
            $user_id = $user->exists() ? $user->id() : 0;

            $res = [
                'id_firm' => $ob['id_firm'],
                'id_parent' => $ob['id_parent'],
                'id_contract' => $contract_id,
                'id_manager' => $ob['id_manager'],
                'id_firm_user' => $user_id,
                'id_service' => $ob['id_service'],
                'company_activity' => $ob['business'] ? $ob['business'] : '',
                'company_address' => $ob['address'] ? $ob['address'] : '',
                'company_cell_phone' => $ob['pager'] ? $ob['pager'] : '',
                'company_email' => $ob['email'] ? $ob['email'] : '',
                'company_fax' => $ob['fax'] ? $ob['fax'] : '',
                'company_map_address' => $ob['mapaddress'] ? $ob['mapaddress'] : '',
                'company_name' => $ob['name'] ? $ob['name'] : $ob['name_ratiss'],
                'company_name_ratiss' => $ob['name_ratiss'] ? $ob['name_ratiss'] : '',
                'company_name_jure' => $ob['jure'] ? $ob['jure'] : '',
                'company_phone' => $ob['phone'] ? $ob['phone'] : '',
                'company_phone_readdress' => $ob['readdres_phone'] ? $ob['readdres_phone'] : '',
                'company_web_site_url' => $ob['web'] ? $ob['web'] : '',
                'mode_work' => $ob['mode_work'] ? $ob['mode_work'] : '',
                'text' => $ob['info'] ? $ob['info'] : '',
                'flag_is_producer' => $ob['producer'],
                'flag_is_active' => $ob['blocked'] ? 0 : 1,
                'file_logo' => '',
                'file_description' => $ob['id_description'] ? $ob['id_description'] : '',
                'path' => $ob['path'] ? $ob['path'] : '',
                'priority' => $ob['priority'] ? $ob['priority'] : '',
                'rating' => 0,
                'id_city' => $ob['id_city'],
                'id_country' => $ob['id_country'],
                'id_region_country' => $ob['id_region_country'],
                'id_region_city' => $ob['id_region_city'],
                'timestamp_inserting' => $ob['date_input'],
                'timestamp_ratiss_updating' => $ob['datetime'],
            ];

            $firm = new Firm();
            $firm->getByIdFirm($ob['id_firm']);
            if ($firm->exists()) {
                $firm->update($res);
            } else {
                $firm->insert($res);
            }
        }

        $this->log('обновили/добавили данные по фирмам');

        $price_updates_file = new CFile(APP_DIR_PATH . '/app/cron/updates/utf8/sts_firm_price_update.txt');
        if ($price_updates_file->exists() && $price_updates_file->getSize() > 0) {
            if ($f = fopen($price_updates_file->path(), "r")) {
                while (!feof($f)) {
                    $line = fgets($f, 4096);
                    if (!$line) {
                        break;
                    }
                    $row = explode("\t", $line);
                    if (count($row) < 3) {
                        break;
                    }
                    list($id_firm, $id_service, $dateupdate) = $row;
                    App::db()->query()->setText('UPDATE `sts_price` SET `datetime` = :datetime WHERE `id_service` = :id_service AND `id_firm` = :id_firm')->execute([':datetime' => $dateupdate, ':id_service' => $id_service, ':id_firm' => $id_firm]);
                }
                fclose($f);
            }
        }

        $this->log('обновили прайсы по обновленным фирмам');

        $this->db->query()->setText("UPDATE `firm` SET `file_logo` = :file_logo WHERE `flag_is_active` = :flag_is_active")->execute([':file_logo' => '', 'flag_is_active' => 1]);

        $j = 0;
        $limit = 1000;
        $offset = -1;

        $this->log('обновляем картинки по обновленным/добавленным фирмам');
        $f = new Firm();
        while (1) {
            $j++;
            $offset++;

            $firms = $f
                    ->setLimits($limit, $limit * $offset)
                    ->setWhere(['AND', 'flag_is_active = :flag_is_active'], [':flag_is_active' => 1])
                    ->getAll();

            if (!$firms) {
                break;
            }

            foreach ($firms as $firm) {
                $image = new Image();
                $image->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'id_price= :nil', 'source = :source'], [':id_firm' => $firm->id_firm(), ':id_service' => $firm->id_service(), ':nil' => 0, ':source' => 'client'])
                        ->setOrderBy('timestamp_inserting DESC')
                        ->getByConds();

                if (!$image->exists()) {
                    $image->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'id_price= :nil', 'source != :source'], [':id_firm' => $firm->id_firm(), ':id_service' => $firm->id_service(), ':nil' => 0, ':source' => 'temp'])
                            ->setOrderBy('timestamp_inserting DESC')
                            ->getByConds();
                }

                if ($image->exists()) {
                    $firm->update(['file_logo' => '/image/' . $image->val('file_subdir_name') . '/' . $image->val('file_name') . '.' . $image->val('file_extension')]);
                }
            }
            $this->log('обработано записей: ' . count($firms));
        }

        $this->log('готово');

        return $this;
    }

    private function loadData($file_name, $table_name = null) {
        if ($table_name === null) {
            $table_name = $file_name;
        }
        $this->log('обновление ' . $table_name);

        $table_fields = App::db()->query()->showCols($table_name);
        $fields = [];
        foreach ($table_fields as $field) {
            $fields[] = $field['Field'];
        }
        $field_str = implode(',', $fields);
        $dump_file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/' . $file_name . '.txt');

        $i = 0;
        if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
            while ($data = fgetcsv($handle, 50000, "	")) {
                if (count($data) !== count($fields)) {
                    $this->log($file_name . '.txt строка ' . $i . ' не соответствие полей');
                } else {
                    $j = 0;
                    $data_string = [];
                    foreach ($fields as $f) {
                        $data_string[] = ':' . $f;
                        $data_params[':' . $f] = $data[$j];
                        $j++;
                    }
                    $data_string = implode(",", $data_string);
                    $query = "REPLACE INTO $table_name($field_str) VALUES($data_string)";
                    if ($field_str && count($data)) {
                        try {
                            App::db()->query()->setText($query)->execute($data_params);
                        } catch (PDOException $exc) {
                            $this->log($exc->getMessage());
                        }
                    }
                }

                $i++;
            }
            fclose($handle);
        }

        $this->log('обработано записей: ' . $i);

        return $this;
    }

    private function deleteStsCost() {
        $this->log('удаление по sts_cost_del');
        $file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/sts_cost_del.txt');
        if ($file->exists() && $file->getSize() > 0) {
            if ($f = fopen($file->path(), "r")) {
                while (!feof($f)) {
                    $line = fgets($f, 4096);
                    $fields = explode("\t", $line);
                    if (count($fields) < 5) {
                        continue;
                    }
                    list($id_cost, $id_price, $id_firm, $id_service, $datetime) = $fields;
                    $query = "DELETE FROM `sts_cost` 
						WHERE 
							`id_price` = :id_price AND 
							`id_firm` = :id_firm AND 
							`id_service` = :id_service AND 
							`id_cost` = :id_cost AND 
							`datetime` < :datetime";
                    App::db()->query()->setText($query)->execute([
                        ':id_price' => (int) $id_price,
                        ':id_firm' => (int) $id_firm,
                        ':id_service' => (int) $id_service,
                        ':id_cost' => (int) $id_cost,
                        ':datetime' => $datetime
                    ]);
                }
                fclose($f);
            }
        }

        return $this;
    }

    private function deleteStsPrice() {
        $this->log('удаление по sts_price_del');
        $file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/sts_price_del.txt');
        if ($file->exists() && $file->getSize() > 0) {
            if ($f = fopen($file->path(), "r")) {
                while (!feof($f)) {
                    $line = fgets($f, 4096);
                    $fields = explode("\t", $line);
                    if (count($fields) < 4) {
                        continue;
                    }
                    list($id_price, $id_firm, $id_service, $datetime) = $fields;
                    $query = "DELETE FROM `sts_price` 
						WHERE 
							`id_price` = :id_price AND 
							`id_firm` = :id_firm AND 
							`id_service` = :id_service AND 
							`datetime` < :datetime";
                    App::db()->query()->setText($query)->execute([
                        ':id_price' => (int) $id_price,
                        ':id_firm' => (int) $id_firm,
                        ':id_service' => (int) $id_service,
                        ':datetime' => $datetime
                    ]);
                }
                fclose($f);
            }
        }

        return $this;
    }

    private function deleteStsFirm() {
        $this->log('удаление по sts_firm_del');
        $file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/sts_firm_del.txt');
        if ($file->exists() && $file->getSize() > 0) {
            $deleted_array = array();
            if ($f = fopen($file->path(), "r")) {
                while (!feof($f)) {
                    $line = fgets($f, 4096);
                    $fields = explode("\t", $line);
                    if (count($fields) < 3) {
                        continue;
                    }
                    list($id_firm, $id_service, $datetime) = $fields;
                    if (!isset($deleted_array[$id_service])) {
                        $deleted_array[$id_service] = [];
                    }
                    $deleted_array[$id_service][] = $id_firm;
                }
                fclose($f);
            }

            if (!empty($deleted_array)) {
                $this->log('удаляем данные, связанные с удаленными фирмами');
                foreach ($deleted_array as $id_service => $id_firms) {
                    foreach ($id_firms as $id_firm) {
                        $sp = new StsPrice();
                        $sc = new StsCost();
                        $where = ['AND', 'id_firm = :id_firm', 'id_service = :id_service'];
                        $params = [':id_firm' => $id_firm, ':id_service' => $id_service];
                        $sp->deleteAll($where, null, null, null, $params);
                        $sc->deleteAll($where, null, null, null, $params);

                        $im = new Image();
                        $image_where = $where;
                        $image_where[] = 'source = :source';
                        $image_params = $params + [':source' => 'ratiss'];
                        $images = $im->setWhere($image_where, $image_params)->getAll();

                        foreach ($images as $im) {
                            $file = new CFile(APP_DIR_PATH . '/public/image/' . $im->val('file_subdir_name') . '/' . $im->val('file_name') . '.' . $im->val('file_extension'));
                            $file->remove();
                            $im->delete();
                        }

                        $firm = new Firm();
                        $firm->getByIdFirm($id_firm);

                        // Проверим наличие банеров, если есть и фирма сейчас будет заблокирована - необходимо уведомить менеджера
                        if (!$firm->isBlocked()) {
                            $sb = new Banner();
                            $today = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(23, 59, 59, date('m'), date('d')));
                            $banners = $sb->setWhere(
                                            ['AND', 'timestamp_ending > :today', 'id_firm = :id_firm', 'id_service = :id_service', 'flag_is_active = :flag_is_active'], [':today' => $today, ':id_firm' => $firm->id_firm(), ':id_service' => $firm->id_service(), ':flag_is_active' => 1])
                                    ->getAll();

                            foreach ($banners as $banner) {
                                if ($firm->id_service() == 10) {
                                    $manager = new FirmManager();
                                    $manager->getByFirm($firm);
                                    if ($manager->exists()) {
                                        $this->sendBlockedFirmBannerNotice($manager->val('email'), $firm, $banner);
                                    }
                                } else {
                                    $service = new StsService();
                                    $service->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
                                            ->getByConds();
                                    if ($service->exists()) {
                                        $this->sendBlockedFirmBannerNotice($service->val('email'), $firm, $banner);
                                    }
                                }
                            }
                        }

                        $firm->update(['flag_is_active' => 0]);
                    }
                }
            }
        }

        return $this;
    }

    private function sendBlockedFirmBannerNotice($email, Firm $firm, Banner $banner) {
        App::email()
                ->setSubject('Уточнение сроков размещения банера для заблокированноый фирмы ' . $firm->name())
                ->setTo($email)
                ->setModel($banner)
                ->setTemplate('email_blocked_firm_banner_notice', 'firmmanager')
                ->sendToQuery();

        return $this;
    }

    private function storeStsPrice() {
        $this->log('обновление таблицы sts_price');

        $dump_file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/sts_price.txt');
        if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
            $i = 0;
            $columns = $this->db->query()->showCols('sts_price');
            unset($columns[0]);
            while (($buffer = fgets($handle)) !== false) {
                $params = [];
                $i++;
                if ($i % 100000 == 0) {
                    $this->log('обработано записей: ' . $i);
                }

                $row = explode('	', str_replace('\\	', ' ', $buffer));
                if (count($row) != 32 || !isset($row[0]) || !isset($row[1]) || trim($row[0]) == '' || trim($row[1]) == '' || $row[0] != intval($row[0]) || $row[1] != intval($row[1])) {
                    $this->log('sts_price.txt проблемы в строке №' . $i . ' (' . count($row) . ') ');
                    continue;
                }

                $sp = new App\Model\StsPrice();
                $sp->getByIdService($row[0], $row[1]);

                $res_row = $row;
                if (isset($res_row[22]) && !$res_row[22]) {
                    $res_row[22] = '0.00';
                }
                if (isset($res_row[24]) && !$res_row[24]) {
                    $res_row[24] = '0';
                }

                foreach ($res_row as $k => $val) {
                    $params[] = ( $val === '') ? '' : trim($val);
                }
                $params[] = 1;
                $params[] = 0;

                $j = 0;
                $upd_row = [];
                foreach ($columns as $col) {
                    $upd_row[$col['Field']] = $params[$j];
                    $j++;
                }

                $image_data = explode('<img style="display:none;" src="" img-data="', $upd_row['info']);

                if (count($image_data) > 1) {
                    $upd_row['info'] = $image_data[0];
                    $image_data = explode('"', array_reverse($image_data)[0]);
                } else {
                    $image_data = null;
                }

                if ($sp->exists()) {
                    $sp->update($upd_row);
                } else {
                    $sp->insert($upd_row);
                }

                if ($image_data != null && file_exists($image_data[0]) && is_file($image_data[0])) {
                    $this->setParsedImage($sp, $image_data[0]);
                }
            }

            $this->db->query()->setText("UPDATE `sts_price` SET `exist_image` = :exist_image")->execute([':exist_image' => 0]);
            $im = new Image();
            $images = $im->setWhere(['AND', 'id_price != :nil'], [':nil' => 0])
                    ->getAll();

            foreach ($images as $image) {
                $this->db->query()->setText("UPDATE `sts_price` SET `exist_image` = :exist_image WHERE `id_price` = :id_price AND `id_service` = :id_service")->execute([':exist_image' => 1, ':id_price' => $image->val('id_price'), ':id_service' => $image->id_service()]);
            }
        } else {
            $this->log('файл обновления не найден');
        }

        return $this;
    }

    private function storeStsPriceImages() {
        $this->log('обновление изображений');

        $dump_file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/images_dump.txt');
        if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
            while (($buffer = fgets($handle)) !== false) {
                $row = explode('	', str_replace('\\	', ' ', $buffer));

                $sp = new App\Model\StsPrice();
                $sp->getByIdService($row[2], $row[1]);

                $image_data = trim($row[3]);

                if ($sp->exists() && $image_data != null && file_exists($image_data) && is_file($image_data)) {
                    $this->setParsedImage($sp, $image_data);
                }
            }

            $this->db->query()->setText("UPDATE `sts_price` SET `exist_image` = :exist_image")->execute([':exist_image' => 0]);
            $im = new Image();
            $images = $im->setWhere(['AND', 'id_price != :nil'], [':nil' => 0])
                    ->getAll();

            foreach ($images as $image) {
                $this->db->query()->setText("UPDATE `sts_price` SET `exist_image` = :exist_image WHERE `id_price` = :id_price AND `id_service` = :id_service")->execute([':exist_image' => 1, ':id_price' => $image->val('id_price'), ':id_service' => $image->id_service()]);
            }
        } else {
            $this->log('файл обновления не найден');
        }

        return $this;
    }

    private function setParsedImage(StsPrice $sp, $image_data) {
        $file_extension = array_reverse(explode('.', $image_data))[0];
        $image = [
            'file_name' => CRandom::uniqueId(),
            'id_firm' => $sp->id_firm(),
            'id_service' => $sp->id_service(),
            'id_price' => $sp->val('id_price'),
            'file_extension' => $file_extension,
            'path' => $image_data,
            'source' => 'auto',
            'timestamp_last_updating' => date('Y-m-d H:i:s')
        ];

        $where = ['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'id_price = :id_price', 'source = :source'];
        $params = [':id_service' => (int) $image['id_service'], ':id_firm' => (int) $image['id_firm'], ':id_price' => (int) $image['id_price'], ':source' => 'auto'];

        $im = new App\Model\Image();
        $im->reader()
                ->setWhere($where, $params)
                ->select();

        //копирование и подготовка изображения
        $target_file_name = $image['file_name'];
        $target_dir = new CDir(APP_DIR_PATH . '/public/image/');
        $target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 0, 1) . '/');
        if (!$target_dir->exists()) {
            $target_dir->create();
        }

        $target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 1, 1) . '/');
        if (!$target_dir->exists()) {
            $target_dir->create();
        }

        $image['file_subdir_name'] = str()->sub($target_file_name, 0, 1) . '/' . str()->sub($target_file_name, 1, 1);
        copy($image['path'], $target_dir->path() . $target_file_name . '.' . $image['file_extension']);
        //unlink($image['path']);
        $__path = $image['path'];
        unset($image['path']);

        if ($im->exists()) {
            $file = new CFile(APP_DIR_PATH . '/public/image/' . $im->val('file_subdir_name') . '/' . $im->val('file_name') . '.' . $im->val('file_extension'));
            $file->remove();

            $im->update($image);
        } else {
            $im->insert($image);
        }
    }

    private function storeStsUsers() {
        $table_name = 'firm_manager';
        $this->log('обновление ' . $table_name);

        $dump_file = new CFile(APP_DIR_PATH . '/app/cron/update/utf8/sts_users.txt');

        $i = 0;
        if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
            while ($data = fgetcsv($handle, 50000, "	")) {
                list($id_user, $name, $login, $pass, $parent_node, $default_email) = $data;
                $row = [
                    'id_user' => (int) $id_user,
                    'name' => $name,
                    'login' => $login,
                    'parent_node' => (int) $parent_node,
                    'email_default' => $default_email
                ];

                if ($id_user) {
                    $fm = new FirmManager();
                    $fm->setWhere(['AND', 'id_user = :id_user'], [':id_user' => (int) $id_user])
                            ->getByConds();

                    if ($fm->exists()) {
                        $fm->update($row);
                    } else {
                        $fm->insert($row);
                    }
                }
                $i++;
            }
            fclose($handle);
        }

        $this->log('обработано записей: ' . $i);

        return $this;
    }

}
