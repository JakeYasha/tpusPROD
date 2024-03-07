<div class="mdc-layout-grid">
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <?= $bread_crumbs?>
    <?/*=$tabs*/?>
    <div class="block-title">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading block-title__heading_h1"><?=$title?></h1>
    </div>
    <p><?=$text?></p>
    <?=$promo?>
    <? if ($items && $childs && $mode === 'goods') {?>
        <h2>Полный рубрикатор каталога товаров</h2>
        <?foreach ($items as $arr) {?>
            <?if(isset($childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']]) && $childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']]){?>
                <div class="module stat-info_v2 brand-list__actions_desktop">
                    <div class="block-title">
                        <h3 class="block-title__heading">
                            <a style="text-decoration: none;color: #34404e;" href="<?= app()->link('/catalog/'.$arr['id_group'].'/'.($mode === 'goods' ? '' : $arr['id_subgroup'].'/'))?>"><?=$arr['web_many_name']?></a>
                        </h3>
                    </div>
                    <br/>
                    <div class="mdc-layout-grid__inner">
                        <?$_i=1;foreach ($childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']] as $child) {?>
                            <?if ($_i !== 10) {?>
                                    <div class="mdc-layout-grid__cell">
                                        <a class="service-item" href="<?=$child['link']?>"><?= $child['name']?></a>
                                    </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=$child['link']?>"><?= $child['name']?></a>
                                            </div>
                            <? }?>
                        <?$_i++;}?>
                        <?if ($_i > 10) {?>
                                        </div>
                                    </div>
                                    <div class="filter-form__expand" id="service-expand-<?=$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                                </div>
                        <?}?>
                    </div>
                </div>
                <div class="module stat-info_v2 brand-list__actions_mobile brand-list__actions_mobile_block">
                    <div class="mdc-layout-grid__inner">
                        <h3 class="block-title__heading mdc-layout-grid__cell">
                            <a style="text-decoration: none;color: #34404e;" href="<?= app()->link('/catalog/'.$arr['id_group'].'/'.($mode === 'goods' ? '' : $arr['id_subgroup'].'/'))?>"><?=$arr['web_many_name']?></a>
                        </h3>
                        <?$_i=1;foreach ($childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']] as $child) {?>
                            <?if ($_i !== 4) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item" href="<?=$child['link']?>"><?= $child['name']?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=$child['link']?>"><?= $child['name']?></a>
                                            </div>
                            <? }?>
                        <?$_i++;}?>
                        <?if ($_i > 4) {?>
                                        </div>
                                    </div>
                                    <div class="filter-form__expand" id="service-expand-<?=$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                                </div>
                        <?}?>
                    </div>
                </div>
            <?}?>
        <?}?>		
    <? } elseif($items && $childs) {?>
        <?if($mode === 'services'){?>
            <h2>Полный рубрикатор каталога услуг</h2>
        <?} else {?>
            <h2>Полный рубрикатор каталога оборудования</h2>
        <?}?>
        <?  foreach ($items as $k=>$val) {?>
            <div class="module stat-info_v2 brand-list__actions_desktop">
                <div class="block-title">
                    <h3 class="block-title__heading">
                        <a style="text-decoration: none;color: #34404e;" href="<?= app()->link($val->link())?>"><?=$val->name()?></a>
                    </h3>
                </div>
                <div class="mdc-layout-grid__inner">
                    <?if(isset($childs[$val->id()]) && $childs[$val->id()]){?>
                        <?$_i=1;foreach ($childs[$val->id()] as $child) {?>
                            <?if ($_i !== 10) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item d5364" href="<?=app()->link($child['link'])?>"><?= $child['name']?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=app()->link($child['link'])?>"><?= $child['name']?></a>
                                            </div>
                            <?}?>
                        <?$_i++;}?>
                        <?if ($_i > 10) {?>
                                    </div>
                                </div>
                                <div class="filter-form__expand" id="service-expand-<?=$val->id()?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                            </div>
                        <?}?>
                    <?}?>
                </div>
            </div>
            <div class="module stat-info_v2 brand-list__actions_mobile brand-list__actions_mobile_block">
                <div class="mdc-layout-grid__inner">
                    <h3 class="block-title__heading mdc-layout-grid__cell">
                        <a style="text-decoration: none;color: #34404e;" href="<?= app()->link($val->link())?>"><?=$val->name()?></a>
                    </h3>
                    <?if(isset($childs[$val->id()]) && $childs[$val->id()]){?>
                        <?$_i=1;foreach ($childs[$val->id()] as $child) {?>
                            <?if ($_i !== 4) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item" href="<?=app()->link($child['link'])?>"><?= $child['name']?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=app()->link($child['link'])?>"><?= $child['name']?></a>
                                            </div>
                            <?}?>
                        <?$_i++;}?>
                        <?if ($_i > 4) {?>
                                    </div>
                                </div>
                                <div class="filter-form__expand" id="service-expand-<?=$val->id()?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                            </div>
                        <?}?>
                    <?}?>
                </div>
            </div>
        <?}?>
    <?} else {?>
        <p>К сожалению, на данный момент, мы не располагаем структурированным каталогом для этого города. Для поиска интересующей Вас информации по другим городам воспользуйтесь выбором города.</p>
    <? }?>
    
<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>