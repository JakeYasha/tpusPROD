<div class="mdc-layout-grid">
    <?= $breadcrumbs?>
    <?/*=app()->chunk()->render('common.print_button')*/?>
    <div class="for_clients clearfix">
        <div class="for_clients_text_c clearfix page for_clients_list">
            <?= $text->val('text')?>
            <?= app()->chunk()->set(['items' => $chart_items_firms, 'title' => 'Количество фирм', 'width' => '50%', 'style' => 'float:left;'])->render('charts.donut')?>
            <?= app()->chunk()->set(['items' => $chart_items_goods, 'title' => 'Количество товаров', 'width' => '50%', 'style' => 'float:left;'])->render('charts.donut')?>
            <table class="cltable" style="width: 100%;">
                <?  foreach ($matrix as $id_region => $id_cities) {if(!$id_cities)continue;?>
                <tr><th colspan="3" style="text-align: center;"><?=$regions[$id_region]?></th></tr>
                <tr>
                    <td>Населенный пункт</td>
                    <td>Количество фирм</td>
                    <td>Количество товаров</td>
                </tr>
                <?foreach ($id_cities as $id_city) {if(!$cities[$id_city])continue;?>
                <tr>
                    <td><?=$counts[$id_city]['firms'] > 100 && $id_city != '76004' ? '<a href="/'.$id_city.'">'.$cities[$id_city].'</a>' : $cities[$id_city]?></td>
                    <td><?=$counts[$id_city]['firms'] > 100 ? ($counts[$id_city]['firms'] ? '<a href="/'.$id_city.'/firm/catalog/" title="'.$cities[$id_city].', каталог фирм">'.$counts[$id_city]['firms'].'</a>' : '-') : $counts[$id_city]['firms']?></td>
                    <td><?=$counts[$id_city]['firms'] > 100 ? ($counts[$id_city]['goods'] ? '<a href="/'.$id_city.'/catalog/" title="'.$cities[$id_city].', каталог товаров">'.$counts[$id_city]['goods'].'</a>' : '-') : $counts[$id_city]['goods']?></td>
                </tr>
                <?}?>
                <?}?>
            </table>
        </div>
    </div>
</div>