<?php

/**
 * @created Aug 22, 2018
 * @author Alexander Voronin <xlor.yar@gmail.com>
 */

namespace App\Action\Crontab;

use App\Model\SubgroupCount;
use Sky4\Exception;
use function app;
use function str;

class SubgroupCounter extends \App\Action\Crontab {

    public function execute() {
        $this->startAction()->log('заполнение таблицы subgroup_count');
        $total_inserted_rows = 0;
        $time = time();

        $_fc = new \App\Model\FirmCity();
        /*$cities = array_keys($_fc->reader()
                        ->setSelect(['DISTINCT(id_city) as `id_city`'])
                        ->rowsWithKey('id_city'));*/
        $this->createTempTables();
        $cities = ['1008'];
        $i = 0;
        foreach ($cities as $city) {
            $firm_ids = array_keys($_fc->reader()
                            ->setWhere(['AND', '`id_city` = :id_city'], [':id_city' => $city])
                            ->rowsWithKey('id_firm'));

            $firm_ids_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($firm_ids, 'id_firm');

            $_price = new \App\Model\Price();
            $groups = $_price->reader()
                    ->setSelect(['DISTINCT(id_group) as `id`'])
                    ->setWhere(['AND', $firm_ids_conds['where'], '`flag_is_active` = :flag_is_active'], [':flag_is_active' => 1] + $firm_ids_conds['params'])
                    ->rows();

            foreach ($groups as $group) {
                $subgroups = $_price->query()
                        ->setSelect(['DISTINCT(id_subgroup) as `id`', 'COUNT(id_subgroup) as `count`'])
                        ->setFrom(['price'])
                        ->setWhere(['AND', $firm_ids_conds['where'], '`id_group` = :id_group', '`flag_is_active` = :flag_is_active'], [':flag_is_active' => 1, ':id_group' => $group['id']] + $firm_ids_conds['params'])
                        ->setGroupBy(['id_subgroup'])
                        ->select();
                
                foreach ($subgroups as $subgroup) {
                    $i++;

                    $sc = new SubgroupCount();
                    $sc->setWhere(['AND', '`id_group` = :id_group', '`id_subgroup` = :id_subgroup', '`id_city` = :id_city'], [
                        ':id_group' => $group['id'],
                        ':id_subgroup' => $subgroup['id'],
                        ':id_city' => $city
                    ])->getByConds();

                    if (!$sc->exists()) {
                        $sc->insert([
                            'id_group' => $group['id'],
                            'id_subgroup' => $subgroup['id'],
                            'id_city' => $city,
                            'count_goods' => $subgroup['count']
                        ]);
                    }
                }
            }

            //echo "\rПрогресс: " . round(($i / $all) * 100, 1) . "% за " . date("H:i:s", time() - $time - 4 * 3600) . " (RAM: " . round(memory_get_usage() / 1024, 1) . "Kb)   ";
        }

        $this->log('Обработано ' . $i, 1);

        //удаляем из sts_subgroup_count количество строк фирмы Справочник, т.к. фирма на показывается в товарах
        $this->cleaning();
        $this->flipTables();

        $this->log('завершено');
        $this->endAction();
    }

    public function cleaning() {
        /* $sp = new StsPrice();
          $sc = new SubgroupCount();

          $f_cnt = $sp->reader()
          ->setWhere(['AND', '`id_service` = :id_service', '`id_firm` = :id_firm', '`blocked` = :0'], [':id_service' => 10, ':id_firm' => 191, ':0' => 0])
          ->count();

          $s_cnt = $sc->setWhere(['AND', '`id_city` = :id_city', '`id_subgroup` = :id_subgroup', '`id_group` = :id_group'], [':id_city' => 76004, ':id_subgroup' => 206, ':id_group' => 35])
          ->getByConds()->val('count_goods');


          $s_cnt = $s_cnt ? (int) $s_cnt : 0;

          if ($s_cnt - $f_cnt <= 0) {
          $this->db->query()
          ->setDeleteFrom(['subgroup_count'])
          ->setWhere(['AND', '`id_city` = :id_city', '`id_subgroup` = :id_subgroup', '`id_group` = :id_group'], [':id_city' => 76004, ':id_subgroup' => 206, ':id_group' => 35])
          ->delete();
          } else {
          $sc = new SubgroupCount();
          $sc->setWhere(['AND', '`id_city` = :id_city', '`id_subgroup` = :id_subgroup', '`id_group` = :id_group'], [':id_city' => 76004, ':id_subgroup' => 206, ':id_group' => 35])
          ->getByConds();

          if ($sc->exists()) {
          $sc->update([
          'count_goods' => $s_cnt - $f_cnt
          ]);
          }
          } */
    }

    public function flipTables() {
        //app()->db()->query()->renameTable('subgroup_count', 'del_subgroup_count');
        //app()->db()->query()->renameTable('tmp_subgroup_count', 'subgroup_count');
        //app()->db()->query()->dropTable('del_subgroup_count');
    }

    public function createTempTables() {
        try {
            app()->db()->query()->dropTable('tmp_subgroup_count');
        } catch (Exception $exc) {
            ;
        }

        app()->db()->query()->copyTable('subgroup_count', 'tmp_subgroup_count');
    }

}
