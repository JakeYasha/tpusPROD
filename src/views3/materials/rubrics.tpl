<nofollow>
    <nav class="nav">
        <a class="nav__link static nav__link-red nav__link-all" href="/materials/" data-category=""><span class="nav__link_inner">ВСЕ</span></a>
        <? $i=-1; foreach($rubrics as $rubric) { $i++; 
            if ($rubrics_count[$rubric->id()]!=='0'){
        ?>
            <a class="nav__link static nav__link-red" href="<?= $rubric->linkItem('materials') ?>" data-category="<?= $i ?>"><span class="nav__link_inner"><?= $rubric->name() ?></span></a>
            <!--<?=$rubrics_count[$rubric->id()];?>-->
        <?      } 
            }?>
            <?if (true==false){?><!--<a class="nav__link static nav__link-blue" href="/afisha/" data-category="<?= $i+1 ?>"><span class="nav__link_inner">Афиша</span></a>
            <a class="nav__link static nav__link-green" href="/news/" data-category="<?= $i+2 ?>"><span class="nav__link_inner">Новости</span></a>--><?}?>
    </nav>
</nofollow>