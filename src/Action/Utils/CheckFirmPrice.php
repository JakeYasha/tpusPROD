<?php

namespace App\Action\Utils;

use \App\Model\Price;
use \App\Model\Firm;

class CheckFirmPrice extends \App\Action\Utils {

    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

    public function execute() {
        ini_set('max_execution_time', 30000);
        $params = app()->request()->processGetParams([
            'id_firm' => ['type' => 'int'],
            'page' => ['type' => 'int'],
        ]);

        $firm = new Firm($params['id_firm']);
        $page = $params['page'] ? $params['page'] : 1;
        if ($firm->exists()) {
            $price = new Price();
            $sql = "SELECT GROUP_CONCAT(DISTINCT d.`g_id` ORDER BY d.`g_id` ASC SEPARATOR ',') AS `ids`
                FROM (SELECT GROUP_CONCAT(DISTINCT p.`id` ORDER BY p.`id` ASC SEPARATOR ',') AS `g_id`, COUNT(*) AS `count` FROM `price` p WHERE id_firm = " . $firm->id() . " GROUP BY LOWER(TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, '&', ' '), '+', ' '), '/', ' '), '-', ' '), ')', ' '), '(', ' '), '.', ' '), ',', ' '), '  ', ' '))) HAVING `count` > 1) d";
            $result = app()->db()->query()
                    ->setText($sql)->fetch()[0];
            $price_ids = explode(',', $result['ids']);
            $price_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($price_ids, 'id');
            
            $pricelist = $price->reader()
                    ->setWhere($price_conds['where'], $price_conds['params'])
                    //->setLimit(1000)
                    ->setOrderBy('timestamp_inserting DESC, name')
                    ->objects();

            echo '<h1 style="text-align:center;">Прайс фирмы ' . $firm->name() . '</h1>';
            echo '<table width="100%" border="1" cellspacing="0">';
            echo '<tr>';
            echo '<th>price.id</th>';
            echo '<th>price.name</th>';
            echo '<th>clones price.id/price.name/image/datetime</th>';
            echo '<th>clones price.is</th>';
            echo '<th>image</th>';
            echo '<th>datetime</th>';
            echo '</tr>';
            $has_image = 0;
            $no_image = 0;
            $has_clones = 0;
            $no_clones = 0;
            $clones_images_count = 0;
            foreach ($pricelist as $item) {
                $_price = $item->prepare();
                if (!$item->val('flag_is_active')) continue;
                if ($_price['image']) continue;
                $_price['clones'] = [];
                $_price['clone_ids'] = [];
                foreach ($pricelist as $_item) {
                    if (trim(strtolower($_price['name'])) == trim(strtolower($_item->name())) 
                            && (int) $_price['id'] != $_item->id()
                            && (int) $_price['id'] > $_item->id()) {
                        $_price['clone_ids'] [] = $_item->id();
                        $_price['clones'] [] = $_item;
                    }
                }
                if ($_price['clones']) {
                    $has_clones++;
                } else {
                    $no_clones++;
                }
                $clones_html = '';
                $clones_has_image = false;
                if ($_price['clones']) {
                    $clones_html .= '<table width="100%" border="1"cellspacing="0">';
                    foreach ($_price['clones'] as $clone) {
                        $clones_html .= '<tr>';
                        $_clone = $clone->prepare();
                        $clones_html .= '<td style="width:100px;text-align:center;">' . $_clone['id'] . '</td>';
                        $clones_html .= '<td style="text-align:center;">' . $_clone['name'] . '</td>';
                        $clones_html .= '<td style="width:170px;text-align:center;">';
                        if ($_clone['image']) {
                            $clones_html .= '<img data-id="' . $_clone['image_id'] . '" src="' . $_clone['image'] . '" alt="' . $_clone['name'] . '" style="width:160px;" />';
                        }
                        $clones_html .= '</td>';
                        $clones_html .= '<td style="width:100px;text-align:center;">' . $clone->val('timestamp_inserting') . '</td>';
                        $clones_html .= '<td style="width:60px;text-align:center;">';
                        if ((int)$_price['id'] && (int)$_clone['image_id']) {
                            $clones_has_image = true;
                            $clones_images_count++;
                            $clones_html .= '<button id="clone_' . $_price['id'] . '" data-price="' . $_price['id'] . '" data-image="' . $_clone['image_id'] . '" onclick="'
                                    //. 'if(confirm(\'Взять изображение от клона для товара #' . $_price['id'] . '?\')) { '
                                    . 'if(true) { '
                                    . 'var xhr = new XMLHttpRequest(); '
                                    . 'xhr.open(\'GET\', \'https://www.tovaryplus.ru/utils/clone-price-image/?id_price=' . (int)$_price['id'] . '&id_image=' . (int)$_clone['image_id'] . '\', false); '
                                    . 'xhr.send(); '
                                    . 'if (xhr.status != 200) { '
                                    . 'alert( xhr.status + \': \' + xhr.statusText ); '
                                    . '} else { '
                                    . 'var btn = document.getElementById(\'clone_' . $_price['id'] . '\').parentElement.parentElement.parentElement.parentElement.parentElement.parentElement; '
                                    . 'btn.style.backgroundColor = \'#43a400\'; '
                                    . '}'
                                . '}'
                                . '">Взять от клона</button>';
                        }
                        $clones_html .= '</td>';
                        $clones_html .= '</tr>';
                    }
                    $clones_html .= '</table>';
                }
                if ($clones_has_image) {
                    echo '<tr>';
                    echo '<td style="width:100px;text-align:center;">' . $_price['id'] . '</td>';
                    echo '<td style="text-align:center;">' . $_price['name'] . '</td>';
                    echo '<td style="text-align:center;">';
                    echo $clones_html;
                    echo '</td>';
                    echo '<td style="text-align:center;">' . join(', ', $_price['clone_ids']) . '</td>';
                    echo '<td style="width:170px;text-align:center;">';
                    if ($_price['image']) {
                        echo '<img data-id="' . $_price['image_id'] . '" src="' . $_price['image'] . '" alt="' . $_price['name'] . '" style="width:160px;" />';
                        $has_image++;
                    } else {
                        $no_image++;
                    }
                    echo '</td>';
                    echo '<td style="width:100px;text-align:center;">' . $item->val('timestamp_inserting') . '</td>';
                    echo '</tr>';
                }
            }

            echo '</table>';

            echo '<br/>Найдено не привязанных картинок: ' . $clones_images_count . '<br/>';

            echo '<br/>С картинками: ' . $has_image . ', без картинок: ' . $no_image . '<br/>';
            echo '<br/>С клонами: ' . $has_clones . ', без клонов: ' . $no_clones . '<br/>';
        } else {
            die('Неправильные параметры');
        }

        die('Выполнено');
    }

}
