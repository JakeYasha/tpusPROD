<? if ($elems && (count($elems) > 1)) {?>
    <ul class="crumbs">
        <?$i = 0;$_i = count($elems);foreach ($elems as $elem) {$i++;?>
            <li class="crumbs__item">
                <?if($i===$_i){?>
                    <span class="crumbs__link crumbs__link_active"><?= $elem['label']?></span>
                <?} else {?>
                    <?= $bread_crumbs->renderElem($elem, 'crumbs__link')?>
                <?}?>
            </li>
        <?}?>
    </ul>
<? }?>
