<div class="mdc-layout-grid">
    <?=$breadcrumbs?>
    <div class="mdc-layout-grid" style="margin-top: 2rem">
        <?=str()->replace($item->val('text'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()])?>
        <?if($item->val('show_sub_page')){?>
            <h2>Дополнительно</h2>
            <ul>
            <?foreach ($childs as $child) {?>
                <li><a href="<?=$child->link()?>"><?=$child->val('name')?></a></li>
            <?}?>
            </ul>
        <?}?>
    </div>
</div>