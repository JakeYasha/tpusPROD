<div class="side_bar index_side_bar">
    <?= app()->chunk()->render('common.header_tp_logo') ?>
    <div class="side_menu_field">
        <? if (count($firms) > 1) { ?>
            <div class="js-sidebar-firm-holder top_menu_item">
                <a href="#" class="cms_tree_current" title="<?= $firm->name() ?>"><?= str()->crop($firm->name(), 22, '...')  ?></a>
            </div>
            <ul class="js-sidebar-firm-holder-options firm-options">
                <? $i = 0;
                $cnt = count($firms);
                foreach ($firms as $frm) {
                    $i++; ?>
                    <li class="<? if ($i === 1) { ?>cms_tree_first<? } ?><? if ($i === $cnt) { ?> cms_tree_last<? } ?>"><a class="firm-user-sidebar-link<? if ($frm['active']) { ?> active<? } ?>" href="/firm-user/set-user/<?= $frm['id_user'] ?>/"><?= $frm['name'] ?></a></li>
                <? } ?>
            </ul><? } else { ?>
            <ul class="main_top_menu">
            <? $i = 0;
            $cnt = count($firms);
            foreach ($firms as $firm) {
                $i++; ?>
                    <li class="<? if ($i === 1) { ?>cms_tree_first<? } ?><? if ($i === $cnt) { ?> cms_tree_last<? } ?>"><a class="firm-user-sidebar-link<? if ($firm['active']) { ?> active<? } ?>" href="/firm-user/set-user/<?= $firm['id_user'] ?>/"><?= $firm['name'] ?></a></li>
                            <? } ?>
            </ul>
<? } ?>
        <ul class="main_middle_menu">
<? foreach ($menu as $elem) { ?>
                <li class="<?= isset($elem['last']) && $elem['last'] ? 'cms_tree_last' : (isset($elem['first']) && $elem['first'] ? 'cms_tree_first' : '') ?><?= $elem['active'] ? ' active' : '' ?>"><div class="clearfix"><a<? if ($elem['count'] === null) { ?> style="width: 100%"<? } ?> href="<?= $elem['link'] ?>" class="name"><?= $elem['name'] ?></a><? if ($elem['count'] !== null) { ?><? if ($elem['count']['new'] > 0) { ?><span class="count new"><a href="<?= $elem['link'] ?>">+<?= $elem['count']['new'] ?></a></span><? } else { ?><span class="count new"><?= $elem['count']['all'] ?></span><? } ?><? } ?></div></li>
<? } ?>
        </ul>
    </div>
</div>