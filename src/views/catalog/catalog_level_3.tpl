<?= $bread_crumbs?>
<?if($items_exists){?>
﻿<div class="cat_description">
	<h1><?=isset($ext_title) && $ext_title ? $ext_title : $title?></h1>
	<?/*if($sub_group->exists() && $sub_group->val('text1') && !$item->id()){?><?=app()->metadata()->replaceLocationTemplates($sub_group->val('text1'))?><?}*/?>
	<?/*=$top_text*/?>
	<?if($top_text){?><p><?=$top_text?></p>
	
	<?} else {?>
		<p>Обзор <?=$filters['mode'] === 'price' ? 'предложений' : 'компаний'?> <?=app()->location()->currentName('genitive')?>. <?=$annotation_text?></p>
	<?}?>
		
	<?if($advert_age_restrictions){?><p>Категория: <?=$advert_age_restrictions?></p><?}?>
</div>
<?/*mobile_filter_here*/?>
<?= app()->chunk()->set('filter', $filters)->set('items', $tags)->set('id_group', $group->id())->set('mode', 'top')->render('common.catalog_tags')?>

<? if ($promo_catalog_data != null) {
    $promo_catalog_id = $promo_catalog_data['price_catalog_id'];
    $promo_catalog_title = $promo_catalog_data['price_catalog_name']; ?>
<div class="rubric_promo clearfix">
        <a class="rubric_promo_button" rel="nofollow" href="<?=app()->link(app()->linkFilter('/advert-module/', ['id_group' => $group->val('id_group'), 'id_subgroup' => $sub_group->val('id_subgroup')]))?>">Акции и скидки в рубрике "<?=$promo_catalog_title?>"</a>
</div>
<? } ?>

<?= app()->chunk()->set('search_string', $item->val('web_name'))->render('adv.catalog_top_banners')?>
<div class="search_result">
	<?=$tabs?>
	<div class="search_result_content">
		<?= $items?>
		<?= $pagination?>
		<div class="attention-info">
			<div><a href="/statistics/dynamic/?id_catalog=<?=$item->id()?>" rel="nofollow">Статистика раздела</a></div>
		</div>
		<div class="bot_text">
			<p>В рубрике  &QUOT;<?=$title?>&QUOT; <?=app()->location()->currentName('prepositional')?> найдено <?=$total_prices_count?> <?=\CWord::ending($total_prices_count, ['предложение','предложения','предложений'])?> в <?=$total_firms_count?> <?=\CWord::ending($total_firms_count, ['фирме','фирмах','фирмах'])?>.</p>
			<p><?=$annotation_text?></p>
		</div>	
		<div class="button_set hr_bottom " style="width: 100%;">
			<a class="get-price royal" style="margin: 0 auto;<?if($filters['mode'] !== 'price'){?>width:302px;<?}?>" href="<?=$filters['mode'] === 'price' ? app()->link(app()->linkFilter($item->link(), array_merge($filters, ['page' => null]), ['mode' => false])) : app()->link(app()->linkFilter($item->link(), array_merge($filters, ['page' => null]), ['mode' => 'price']))?>">Посмотреть все <?=$filters['mode'] === 'price' ? 'фирмы' : 'предложения'?></a>
		</div>
		<div class="bot_text">
			<?if($bottom_text){?><?=$bottom_text?><?}?>
		</div>
	</div>
</div>
<?=$promo_items?>
<?if($analog_catalogs){?>
<div class="black-block">Предложения товаров и услуг в других категориях по запросу "<?=$item->val('name')?>"</div>
<?= app()->chunk()->set('items', $analog_catalogs)->set('id_group', $group->id())->set('mode', 'bottom')->render('common.catalog_tags')?>
<?}?>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>
<?if($sub_group->exists() && $sub_group->val('text2') && !$item->id()){?><div class="cat_description"><?=app()->metadata()->replaceLocationTemplates($sub_group->val('text2'))?></div><?}?>
<?=$advert_restrictions?>
<?if($advert_age_restrictions){?><p>Категория: <?=$advert_age_restrictions?></p><?}?>
<?} else {?>
<div class="cat_description">
	<h1>Запрашиваемая Вами информация не найдена</h1>
	<p>Возможно, запрашиваемая Вами информация была перенесена или удалена. Воспользуйтесь ссылками для перехода в другие разделы каталога.</p>
</div>
	<?if($advert_age_restrictions){?><p>Категория: <?=$advert_age_restrictions?></p><?}?>
	<?= app()->chunk()->set('filter', [])->set('items', $no_items_groups)->set('id_group', $group->id())->render('common.catalog_tags')?>
	
<div class="cat_description">
	<p>Пожалуйста, воспользуйтесь навигацией или формой поиска, чтобы найти интересующую Вас информацию</p>
</div>
<?=$advert_restrictions?>
<?}?>