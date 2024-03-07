<nav class="nav nav-green">
    <? $i=-1; foreach($rubrics as $rubric) { $i++; ?>
        <a class="nav__link static nav__link-green" href="<?= $rubric->linkItem('news') ?>" data-category="<?= $i ?>"><span class="nav__link_inner"><?= $rubric->name() ?></span></a>
    <? } ?>
        <a class="nav__link static nav__link-red" href="/materials/" data-category="<?= $i+1 ?>"><span class="nav__link_inner">Газета</span></a>
        <a class="nav__link static nav__link-blue" href="/afisha/" data-category="<?= $i+2 ?>"><span class="nav__link_inner">Афиша</span></a>

</nav>