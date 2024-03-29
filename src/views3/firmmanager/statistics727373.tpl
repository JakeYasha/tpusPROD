<?= $bread_crumbs ?>
<a title="Выгрузить в xls" target="_blank" class="download-btn xls_s_file" href="<?= app()->link(app()->linkFilter('/firm-manager/statistics727373/', $filters, ['export' => 'xls'])) ?>"></a>
<a title="Выгрузить в pdf" target="_blank" class="download-btn pdf_s_file" href="<?= app()->link(app()->linkFilter('/firm-manager/statistics727373-pdf/', $filters)) ?>"></a>
<div class="black-block">Запросы</div>
<div class="search_result" style="border-top: none;">
    <div class="search_price_field">
        <form action="/firm-manager/statistics727373/" method="get">
            <div class="notice-dark-grey firm-user-banners">
                <? if ($firms) { ?>
                    <br/><label>Фирма</label><select style="font-size:15px;" name="id_firm" class="def">
                        <option value="0">---</option>
                        <? foreach ($firms as $item) { ?>
                            <option <? if ((int) $filters['id_firm'] === (int) $item->id()) { ?> selected="selected"<? } ?> value="<?= $item->id() ?>"><?= $item->name() ?></option>
                        <? } ?>
                    </select>
                <? } ?>
                <br/>
                <label>c</label><input type="text" name="t_start" id="datepicker_start" value="<?=$filters['t_start']?>"/><br/>
                <label>по</label><input type="text" name="t_end" id="datepicker_end" value="<?=$filters['t_end']?>"/><br/>
                <br/>
                <br/>
                <br/>
                <button type="submit" style="width: 200px;">Поиск</button>
            </div>
        </form>
    </div>
    <div class="delimiter-block"></div>
    <? if ($items_count > 0) { ?>
        <div class="cat_description">
            <p>Найдено: <?= $items_count ?></p>
        </div>
    <? } ?>
    <?= $items ?>
    <?= $pagination ?>
</div>