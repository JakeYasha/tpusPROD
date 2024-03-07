<div class="mdc-layout-grid">
    <?= $breadcrumbs?>
    <?/*=app()->chunk()->render('common.print_button')*/?>
    <div class="for_clients clearfix">
        <div class="for_clients_text_c clearfix page for_clients_list">
            <h1>Статистика просмотров категории &quot;<?=$item->name()?>&quot;</h1>
            <p>В отчете показано количество просмотров страниц категории &quot;<?=$item->name()?>&quot; и родительских рубрик каталога товаров и услуг за последние 6 месяцев с текущего дня.</p>
            <?=  app()->chunk()->set('items', $chart_items)->set('title', '')->render('charts.line')?>
            <table class="cltable" style="width: 100%;">
                <tr>
                    <th>Раздел</th>
                    <?  foreach ($months as $k=>$v){?>
                    <th><?=  str()->firstCharToUpper($v)?></th>
                    <?}?>
                </tr>
                <?  foreach ($items as $item) {?>
                <tr>
                    <td><?=$item['space']?><a href="<?=$item['link']?>"><?=$item['name']?></a></td>
                    <?  foreach ($months as $k=>$v){?>
                    <td><?=  isset($item['stats'][$k]) ? $item['stats'][$k]['count'] : '-'?></td>
                    <?}?>
                </tr>
                <?}?>
            </table>
            <br/>
            <h2>Посещаемость в разрезе городов</h2>
            <p>В отчете показано соотношение количества просмотров страниц категории в разрезе регионов поиска информации за последние 6 месяцев с текущего дня.</p>
            <?=  app()->chunk()->set('items', $donut_chart_items)->set('title', '')->render('charts.donut')?>
        </div>
    </div>
</div>