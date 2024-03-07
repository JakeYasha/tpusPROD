<?php

namespace App\Model\Firm;

use App\Model\Firm;
use Sky4\Model;

class Reader extends \Sky4\Model\Reader {

    /**
     * @return array
     */
    public function suggest($q, $field_name = 'name', $rel_fields = []) {
        $q = \Sky4\Helper\StringHelper::trim($q);
        $field_name = (string) $field_name;
        if ($q && $field_name) {
            $id_fields_names = $this->model()->idFieldsNames();
            if (is_array($id_fields_names) && (count($id_fields_names) === 1) && isset($id_fields_names[0]) && $this->model()->fieldExists($id_fields_names[0])) {
                $select = ['`' . $id_fields_names[0] . '` AS `key`', 'CONCAT(`' . $field_name . '`," [",`id_firm`,"/",`id_service`,"]") AS `val`'];
                $where = ['AND', '`' . $field_name . '` LIKE :' . $field_name];
                $params = [':' . $field_name => '%' . $q . '%'];
                foreach ($rel_fields as $rel_field_name => $rel_field_val) {
                    $where[] = '`' . $rel_field_name . '` = :' . $rel_field_name;
                    $params[':' . $rel_field_name] = $rel_field_val;
                }
                if ($this->getLimit() === null) {
                    $this->setLimit(20);
                }
                return $this->setSelect($select)
                                ->setWhere($where, $params)
                                ->setGroupBy($this->getGroupBy())
                                ->setOrderBy($this->getOrderBy())
                                ->rows();
            }
        }
        return [];
    }

}
