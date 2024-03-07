<nav class="nav nav-blue">
    <? $i=-1; foreach($rubrics as $rubric) { $i++; ?>
        <a class="nav__link static nav__link-blue" href="<?= $rubric->linkItem('afisha') ?>" data-category="<?= $i ?>"><span class="nav__link_inner"><?= $rubric->name() ?></span></a>
    <? } ?>
        <a class="nav__link static nav__link-red" href="/materials/" data-category="<?= $i+1 ?>"><span class="nav__link_inner">Газета</span></a>
        <a class="nav__link static nav__link-green" href="/news/" data-category="<?= $i+2 ?>"><span class="nav__link_inner">Новости</span></a>
        <a class="nav__link static nav__link-red" style="color: #FFF!important;padding-left: 10px;" href="/page/handbook/?n=1">Новогодние предложения</a>
    
</nav>