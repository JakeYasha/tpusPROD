<?php

ini_set('memory_limit', '1G');
ini_set('max_execution_time', -1);
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set("log_errors", 0);

system('mysql -u root -pXaquRmC3mWksnAA6vDWX tovaryplus_dev < /var/www/banners_old.sql');

$olddb = new \Sky4\Db\Connection('old');
$tables = [
    'sts_web_firm_adversting' => 'id_web_firm_adversting',
    'sts_web_adversting_group' => 'id_web_adversting_group'
];

foreach ($tables as $table => $id_row_name) {
    $offset = 0;
    $count_rows = 0;
    $chunk = 100000;
    $start_datetime = (new Sky4\Helper\DeprecatedDateTime())->fromTimestamp(mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
    $end_datetime = (new Sky4\Helper\DeprecatedDateTime())->fromTimestamp(mktime(0, 0, 0, date('m'), date('d'), date('Y')));

    while (1) {
        $_query = 'SELECT * FROM `' . $table . '` WHERE `datetime` BETWEEN \'' . $start_datetime . '\' AND \'' . $end_datetime . '\' ORDER BY `id_web_firm_adversting` ASC LIMIT ' . $offset . ', ' . $chunk;

        $rows = $olddb->query()->setText($_query)
                ->fetch();

        print_r(PHP_EOL . $table);

        if (!$rows) {
            print_r(PHP_EOL . 'end: ' . $count_rows);
            break;
        }

        foreach ($rows as $row) {
            if (isset($row['id_firm']) && isset($row['id_service'])) {
                $firm_id = app()->db()->query()->setText('SELECT id FROM `firm` WHERE id_firm = ' . $row['id_firm'] . ' AND id_service = ' . $row['id_service'])->fetch();
                if (isset($firm_id[0]) && $firm_id[0]) {
                    if (isset($row['id_service'])) {
                        unset($row['id_service']);
                    }
                    if (isset($row['virtual_id_firm'])) {
                        unset($row['virtual_id_firm']);
                    }
                    if (isset($row['id_web_site']) && $row['id_web_site'] != 18) {
                        continue;
                    }
                    $row['id_firm'] = $firm_id[0]['id'];
                    $_set = [];
                    foreach ($row as $field_name => $val) {
                        $_set['`' . (string) $field_name . '`'] = $val;
                    }

                    $rows = app()->db()->query()
                            ->setFrom($table)
                            ->setWhere(['AND', $id_row_name . ' = :' . $id_row_name], [':' . $id_row_name => $row[$id_row_name]])
                            ->select();

                    if ($rows && count($rows) > 0) {
                        app()->db()->query()
                                ->setUpdate('`' . $table . '`')
                                ->setSet($_set)
                                ->setWhere(['AND', $id_row_name . ' = :' . $id_row_name], [':' . $id_row_name => $row[$id_row_name]])
                                ->update();
                    } else {
                        app()->db()->query()
                                ->setInsertInto('`' . $table . '`')
                                ->setSet($_set)
                                ->insert();
                    }

                    $count_rows++;
                }
            } else {
                $_set = [];
                foreach ($row as $field_name => $val) {
                    $_set['`' . (string) $field_name . '`'] = $val;
                }

                $rows = app()->db()->query()
                        ->setFrom($table)
                        ->setWhere(['AND', $id_row_name . ' = :' . $id_row_name], [':' . $id_row_name => $row[$id_row_name]])
                        ->select();

                if ($rows && count($rows) > 0) {
                    app()->db()->query()
                            ->setUpdate('`' . $table . '`')
                            ->setSet($_set)
                            ->setWhere(['AND', $id_row_name . ' = :' . $id_row_name], [':' . $id_row_name => $row[$id_row_name]])
                            ->update();
                } else {
                    app()->db()->query()
                            ->setInsertInto('`' . $table . '`')
                            ->setSet($_set)
                            ->insert();
                }

                $count_rows++;
            }
            if ($count_rows % 1000 === 0) {
                print_r("\r" . $count_rows);
            }
        }
        $offset += $chunk;
    }
}