<?if($tabs){
    $ok_hide = false;
    if (!$hide_sorting){
        $ok_hide = true;//если предопределено скрытие, то не скрываем - это быдлотриггер короче
        $hide_sorting = true;
    }
    ?>
    <div class="filter-list__sortbox">
        <?if($counters){?><span><h3 class="block-title__heading">Найдено:</h3></span><?}?>
        <?$i=-1;foreach($tabs as $tab){$i++;if(isset($tab['display']) && $tab['display']===false)continue;
            if($tab['label'] === 'На карте')continue;

            if($active_tab_index===$i){?>
                <span><h3 class="block-title__heading block-title__heading_accent"><?=$tab['label']?><?if(trim($tab['label'])=="Товары"){if ($ok_hide){$hide_sorting=false;}}?> <?if(isset($counters[$i]) && $counters[$i] !== null){?> <?=$counters[$i]?><?}?></h3></span>
            <?} else {?>
                <span><h3 class="block-title__heading<?if(isset($tab['disabled']) && $tab['disabled']===true){?> disabled<?}?>"><?if(isset($tab['disabled']) && $tab['disabled']===true){?><?=$tab['label']?><?if(isset($counters[$i]) && $counters[$i] !== null){?> <?=$counters[$i]?><?}?><?} else {?><a style="text-decoration: none;color: #34404e;" <?if(isset($tab['nofollow']) && $tab['nofollow']===true){?>rel="nofollow"<?}?> href="<?=$tab['link']?>"><?=$tab['label']?><?
                if((isset($tab['disabled']) && $tab['disabled']!=true) && (isset($counters[$i]) && $counters[$i] !== null)){?> <?=$counters[$i]?><?}?>
                        </a><?}?></h3></span>
            <?}?>
        <?}?>
        <?if($sorters && !$hide_sorting){?>
            <form action="<?=isset($link) && !empty($link) ? $link : '#'?>" method="get">
                <? foreach ($filters as $k=>$v) {if($v){?>
                    <input type="hidden" name="<?=$k?>" value="<?=$v?>" />
                    <?}?>
                <?}?>
                <select name="sorting" id="sort_by" class="def sort-filter form__control brand-list__action brand-list__form-control js-onchange-sorting">
                    <?foreach ($sorters as $sort_key => $sorter){?>
                        <option<?if($active_sort_option === $sort_key){?> selected="selected"<?}?> value="<?=$sort_key?>"><?=str()->replace($sorter['name'], ' ', '&nbsp;')?></option>
                    <?}?>
                </select>
            </form>
        <?}?>
        <?if($show_display_modes){?>
            <?= app()->chunk()->set('filters', $filters)->set('link', $link)->render('common.display_mode')?>
        <?} elseif(app()->getVar('on_map_link', false)) {?>
            <a rel="nofollow" class="sort-filter btn btn_outline--primary brand-list__action" href="<?=app()->getVar('on_map_link')?>">на карте</a>
        <?}?>
    </div>
<?}?>