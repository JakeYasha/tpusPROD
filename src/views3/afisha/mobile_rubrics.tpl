<ul class="list menu-list">
    <li style="background-color: #bf3745;font-weight: bold;"> <a class="mobile-menu__link" style="color: #FFF!important;padding-left: 10px;" href="/materials/"> > ГАЗЕТА "Товары+"</a></li>
    <li style="background-color: #bf3745cc;font-weight: bold;"> <a class="mobile-menu__link" style="color: #FFF!important;padding-left: 10px;" href="/page/handbook/">Электронный справочник</a></li>
    <li style="background-color: #bf3745cc;font-weight: bold;"> <a class="mobile-menu__link" style="color: #FFF!important;padding-left: 10px;" href="/page/handbook/?n=1">Новогодние предложения</a></li>
    <? $i=-1; foreach($rubrics as $rubric) { $i++; if(!isset($items[$i])) continue; ?>
        <li><a class="mobile-menu__link mobile-menu__link_with-sub" href=""><?= $rubric->name() ?></a>
            <? foreach ($items as $index => $level1) { if(!$level1 || ($index != $i)) continue; ?>
                <ul class="sub list">
                    <?  foreach ($level1 as $id=>$val) {$sub = $val['item'];?>
                        <li><a class="mobile-menu__link mobile-menu__link_with-sub" href="<?= $sub->link() != '#' ? app()->link($sub->link()) : '#' ?>"><?= $sub->name() ?></a>
                            <ul class="sub list">
                                <? foreach($val['subs'] as $k=>$child) { ?>
                                    <li><a class="mobile-menu__link" href="<?= app()->link($child->link()) ?>"><?= $child->name() ?></a></li>
                                <? } ?>
                            </ul>
                        </li>
                    <? } ?>
                </ul>
            <? } ?>
        </li>
    <? } ?>
</ul>