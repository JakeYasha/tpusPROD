<?= $bread_crumbs?>
<?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
?>
<div class="search-result" style="border-top: none; margin-top: 0px;">
	<?=$tabs?>
</div>
<div class="cat_description">
	<h1><?=$title?></h1>
	<?=$text?>
</div>
<?if($rubrics !== '') {?>
<br/>
<?=$rubrics?>
<?}?>
<?=$promo?>
<div class="clearfix">
	<div class="clearfix firm-catalog">
<? if ($items && $childs && $mode === 'goods') {?>
		<div class="black-block">Полный рубрикатор каталога товаров</div>
		<?foreach ($items as $arr) {?>
			<?if(isset($childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']]) && $childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']]){?>
			<div class="headline"><a href="<?= app()->link('/catalog/'.$arr['id_group'].'/'.($mode === 'goods' ? '' : $arr['id_subgroup'].'/'))?>"><?=$arr['web_many_name']?></a></div>
			<div class="tags_field">
				<ul>
					<?foreach ($childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']] as $child) {?>
						<li><a href="<?=$child['link']?>"><?= $child['name']?></a></li>
					<?}?>
				</ul>
				<? if (count($childs[$mode === 'goods' ? $arr['id_group'] : $arr['id_subgroup']]) > 6) {?>
					<div class="show_more">
						<div class="line"></div>
						<a href="#" class="js-show-more-tags"><span>Показать все</span></a>
					</div>
				<? }?>
			</div>
			<?}?>
		<?}?>		
<? } elseif($items && $childs) {?>
		<?if($mode === 'services'){?>
			<div class="black-block">Полный рубрикатор каталога услуг</div>
		<?} else {?>
			<div class="black-block">Полный рубрикатор каталога оборудования</div>
		<?}?>
			<?  foreach ($items as $k=>$val) {?>
			<div class="headline"><a href="<?= app()->link($val->link())?>"><?=$val->name()?></a></div>
			<?if(isset($childs[$val->id()]) && $childs[$val->id()]){?>
			<div class="tags_field">
				<ul>
				<?  foreach ($childs[$val->id()] as $child) {?>
				<li><a href="<?=app()->link($child['link'])?>"><?= $child['name']?></a></li>
				<?}?>
				</ul>
				<? if (count($childs[$val->id()]) > 6) {?>
					<div class="show_more">
						<div class="line"></div>
						<a href="#" class="js-show-more-tags"><span>Показать все</span></a>
					</div>
				<? }?>
			</div>
			<?}?>
			<?}?>
		<?/*$sorted_groups = [];foreach ($matrix as $id_subgroup => $id_childs) {$sorted_groups[$items[$id_subgroup]['name']] = ['items' => $id_childs, 'id_subgroup' => $id_subgroup];}?>
		<?  ksort($sorted_groups);foreach ($sorted_groups as $name => $childs_gr) {$id_childs = $childs_gr['items']; $id_subgroup = $childs_gr['id_subgroup'];?>
			<h2><a href="<?= app()->link('/catalog/'.$id_group.'/'.$id_subgroup.'/')?>"><?=$items[$id_subgroup]['name']?></a></h2>
			<?if($id_childs) {?>
			<div class="tags_field">
				<ul>
					<?$sorted = [];foreach ($id_childs as $id_child) {$child = $childs[$id_child];$sorted[$child->name()] = $id_child;}?>
					<?ksort($sorted);foreach ($sorted as $name => $id) {$child = $childs[$id];?>
						<li><a href="<?=app()->link($child->link())?>"><?= $child->name()?></a></li>
					<?}?>
				</ul>
				<? if (count($id_childs) > 6) {?>
					<div class="show_more">
						<div class="line"></div>
						<a href="#" class="js-show-more-tags"><span>Показать все</span></a>
					</div>
				<? }?>
			</div>
			<?}?>
		<?}*/?>
<?} else {?>
		<div class="cat_description">
			<p>К сожалению, на данный момент, мы не располагаем структурированным каталогом для этого города. Для поиска интересующей Вас информации по другим городам воспользуйтесь выбором города.</p>
		</div>
<? }?>
	</div>
</div>
<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
?>