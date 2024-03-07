<? if ($items) { ?>
    <? $i = 0;
    foreach ($items as $item) {
        $i++; ?>
        <div class="search_result_cell no_span"><span class="number"><?= $i ?></span>
            <div class="title"></div>
            <div class="description" style="font-size: 10px;padding: 5px 0 8px 5px;color: #ad7d7d;">
            </div>
            <div class="description notice-blue">
            </div>
        </div>
    <? } ?>
<? } ?>