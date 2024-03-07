<div class="mdc-layout-grid">
    <?= $bread_crumbs?>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <div class="for_clients clearfix">
        <div class="for_clients_text_c clearfix page">
            <?=$item->val('text')?>
            <table class='cltable'>
                <tbody>
                    <tr>
                        <th>
                            <strong>Город</strong>
                        </th>
                        <th>
                            <strong>Название службы</strong>
                        </th>
                        <th>
                            <strong>Сайт</strong>
                        </th>
                        <th>
                            <strong>Фирм</strong>
                        </th>
                    </tr>
                    <?foreach ($cities as $row) {?>
                    <tr>
                        <td><?=$row[0]?></td>
                        <td><?=$row[1]?></td>
                        <td><?=$row[2]?></td>
                        <td><?=$row[3]?></td>
                    </tr>
                    <?}?>
                </tbody>
            </table>
            <br/>
            <p><strong>Всего: <?=$count_cities?> городов</strong></p>
        </div>
    </div>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>