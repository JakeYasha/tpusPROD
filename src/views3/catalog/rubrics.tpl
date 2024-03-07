<nofollow>
<nav class="nav">
    <? $i=-1; foreach($rubrics as $rubric) { $i++; if(!isset($items[$i])) continue; ?>
        <a rel="nofollow" class="nav__link" href="" data-category="<?= $i ?>"><span class="nav__link_inner"><?= $rubric->name() ?></span></a>
    <? } ?>
    <a rel="nofollow" class="nav__link static" href="<?= app()->link('/advert-module/')?>"><span class="nav__link_inner">Акции, Скидки</span></a>
    <a rel="nofollow" class="nav__link" href="/page/handbook/"><span class="nav__link_inner"><b>Электронный справочник</b></span></a>
    <a rel="nofollow" class="nav__link" href="/materials/"><span class="nav__link_inner"><b>ГАЗЕТА Товары+</b></span></a>
    <a rel="nofollow" class="nav__link" href="/page/handbook/?n=1"><span class="nav__link_inner"><b>Новогодние предложения</b></a>
    
</nav>
<div class="nav-menu">
    <div class="nav-menu__container">
        <? $i=-1; foreach ($items as $index => $level1) { if(!$level1) continue; ?>
            <div class="nav-menu__category" data-category="<?=$index?>">
                <? foreach ($level1 as $id => $val) { $sub = $val['item']; ?>
                    <div class="nav-menu__column">
                        <a rel="nofollow" class="nav-menu__category--heading" href="<?= $sub->link() != '#' ? app()->link($sub->link()) : '#' ?>"><?= $sub->name() ?></a>
                        <? foreach($val['subs'] as $k=>$child) { ?>
                            <a rel="nofollow" href="<?= app()->link($child->link()) ?>"><?= $child->name() ?></a>
                        <? } ?>
                    </div>
                <? } ?>
            </div>
        <? } ?>
    </div>
</div>
</nofollow>