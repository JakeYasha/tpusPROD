<div class="mdc-layout-grid">
    <?= $bread_crumbs?>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <?if($region_stat) {?>
        <div class="cat_description">
            <p>В разделе представлены каталоги фирм по городам региона <?=app()->location()->currentName()?>. Выберите для дальнейшего просмотра интересующий вас город региона.</p>
        </div>			
        <div class="tags_field">
            <ul>
                <?foreach($region_stat as $row) {?>
                <li><a href="/<?=$row['id_city']?>/firm/catalog/"><?=$row['name']?> (<?=$row['count_firms']?>)</a></li>
                <?}?>
            </ul>
            <? if (count($region_stat) > 6) {?>
                <div class="show_more">
                    <div class="line"></div>
                    <a href="#" class="js-show-more-tags"><span>Показать все</span></a>
                </div>
            <? }?>
        </div>
    <?}?>

    <div class="block-title">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading block-title__heading_h1"><?=$header?></h1>
        <p>В тематических рубриках каталога фирм представлены списки государственных, муниципальных и частных компаний, предприятий, организаций <?=app()->location()->currentName('genitive')?> сгруппированных по направлениям деятельности фирм.</p>
        <p><?=str()->replace(app()->config()->get('app.firm.catalog', ''), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()])?></p>
    </div>
    <?/*$tabs*/?>
    <div class="module stat-info_v2">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell">
                <a class="service-item" href="<?=app()->link('/firm/new/')?>">Новые фирмы</a>
            </div>
            <div class="mdc-layout-grid__cell">
                <a class="service-item" href="<?=app()->link('/firm/popular/')?>">Популярные фирмы</a>
            </div>
            <div class="mdc-layout-grid__cell">
                <a class="service-item" href="<?=app()->link('/firm/best/')?>">Лидеры по рейтингу</a>
            </div>
        </div>
    </div>
    <? if ($mode === 'catalog') {?>
        <br/>
        <h2>Полный рубрикатор каталога фирм</h2>
        <? if ($items) {?>
            <?$j=0;$half=ceil(count($items)/2);foreach ($items as $mtKey => $arr) {$j++;$mtKey = $arr['mtkey'];?>
                <div class="module stat-info_v2 brand-list__actions_desktop">
                    <div class="block-title">
                        <h3 class="block-title__heading">
                            <a style="text-decoration: none;color: #34404e;" href="<?= app()->link('/firm/bytype/' . $mtKey . '/')?>"><?= $main_types[$mtKey]['name']?></a>
                        </h3>
                    </div>
                    <br/>
                    <div class="mdc-layout-grid__inner">
                        <?$_i=1;foreach ($arr['childs'] as $child) {?>
                            <?if ($_i !== 10) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item" href="<?=app()->link('/firm/bytype/'.$mtKey.'/'.$child['id'].'/')?>"><?= $child['name']?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=app()->link('/firm/bytype/'.$mtKey.'/'.$child['id'].'/')?>"><?= $child['name']?></a>
                                            </div>
                            <? }?>
                        <?$_i++;}?>
                        <?if ($_i > 10) {?>
                                        </div>
                                    </div>
                                    <div class="filter-form__expand" id="service-expand-d-<?=$j?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                                </div>
                        <?}?>
                    </div>
                </div>
                <div class="module stat-info_v2 brand-list__actions_mobile  brand-list__actions_mobile_block">
                    <div class="mdc-layout-grid__inner">
                        <h3 class="block-title__heading mdc-layout-grid__cell">
                            <a style="text-decoration: none;color: #34404e;" href="<?= app()->link('/firm/bytype/' . $mtKey . '/')?>"><?= $main_types[$mtKey]['name']?></a>
                        </h3>
                        <?$_i=1;foreach ($arr['childs'] as $child) {?>
                            <?if ($_i !== 4) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item" href="<?=app()->link('/firm/bytype/'.$mtKey.'/'.$child['id'].'/')?>"><?= $child['name']?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=app()->link('/firm/bytype/'.$mtKey.'/'.$child['id'].'/')?>"><?= $child['name']?></a>
                                            </div>
                            <? }?>
                        <?$_i++;}?>
                        <?if ($_i > 4) {?>
                                        </div>
                                    </div>
                                    <div class="filter-form__expand" id="service-expand-m-<?=$j?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                                </div>
                        <?}?>
                    </div>
                </div>
            <?}?>
        <? } else {?>
            <div class="cat_description">
                <p>К сожалению, на данный момент, мы не располагаем структурированным каталогом фирм для этого города.</p>
            </div>
        <?}?>
    <? } else {?>
    <div class="clearfix">
        <div class="for_clients_text_c clearfix firm-catalog firm-catalog-list alphabet">
            <ul>
            <?foreach ($alphabet as $a) {$a = $a['name'];?>
                <li><a href="<?=app()->link('/firm/catalog/alphabet/'.encode($a).'/')?>"><?=$a?></a></li>
            <?}?>
            </ul>
        </div>
    </div>
    <?}?>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>