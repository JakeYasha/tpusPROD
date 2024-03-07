<nofollow>
    <ul class="list menu-list sidebar-list material-ul-rubric-mobile">
        <? if ($rubrics) {?>


            <li class="sidebar-list__item"><a class="mobile-menu__link" href="/materials/">ВСЕ</a><span><?=$rubrics_count['all'];?></span></li>
            <?foreach($rubrics as $rubric) 
                { 
                    if ($rubrics_count[$rubric->id()]>0){
                    ?>
                    <li class="sidebar-list__item"><a class="mobile-menu__link" href="<?= $rubric->linkItem('materials') ?>"><?= $rubric->name() ?></a><span><?=$rubrics_count[$rubric->id()];?></span></li>
                    <? 

                    } 
                }?>
        <? } ?>
    </ul>
</nofollow>

