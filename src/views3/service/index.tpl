<div class="mdc-layout-grid">
    <?= $breadcrumbs?>
    <div class="for_clients clearfix">
        <div class="for_clients_text_c clearfix page for_clients_list">
            <?= $item->val('text')?>
            <? foreach ($items as $item) {?>
                <a name="service<?= $item->id()?>"></a>
                <?  if ($_SERVER['REMOTE_ADDR'] == '46.47.52.222') {?>
                <h1><?= $item->name()?><?=$item->id()?></h1>
                   <?}?>
                <? if ($item->val('text')) {?>
                    <?=str()->replace($item->val('text'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()])?>
                <? }?>
                <?= $item->val('price') ? $item->val('price') : ''?>
            <? }?>
        </div>
    </div>
</div>