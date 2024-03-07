<?= $bread_crumbs?>
<div class="cat_description">
	<h1><?=isset($ext_title) && $ext_title ? $ext_title : $title?></h1>
	<p>На страницах каталога товаров "<?=$group->name()?>" вы найдете рекламные и справочно-информационные материалы и сведения о фирмах <?=app()->location()->currentName('prepositional')?>, а также предложения товаров, относящихся к тематике каталога. Торговые и другие отраслевые компании представляют к вашему вниманию свои прайс-листы, цены, фото и видеоматериалы на товары категории - <?=$group->name()?>.
	<p>Для просмотра интересующих вас предложений, выберите соответствующую рубрику.</p>
</div>
<?=$promo?>
<?if($group->exists() && $group->val('text1')){?><div class="cat_description"><?=app()->metadata()->replaceLocationTemplates($group->val('text1'))?></div><?}?>
<div class="clearfix">
	<div class="clearfix firm-catalog">
	<div class="black-block">Список категорий каталога товаров - <?=$group->name()?></div>
<?if($matrix && $id_group) {?>
		<?$sorted_groups = [];foreach ($matrix as $id_subgroup => $id_childs) {$sorted_groups[$items[$id_subgroup]['name']] = ['items' => $id_childs, 'id_subgroup' => $id_subgroup];}?>
		<?  ksort($sorted_groups);foreach ($sorted_groups as $name => $childs_gr) {$id_childs = $childs_gr['items']; $id_subgroup = $childs_gr['id_subgroup'];?>
			<div class="headline"><a href="<?= app()->link('/catalog/'.$id_group.'/'.$id_subgroup.'/')?>"><?=$items[$id_subgroup]['name']?></a></div>
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
		<?}?>
		<?if($group->exists() && $group->val('text2')){?><div class="cat_description"><?=app()->metadata()->replaceLocationTemplates($group->val('text2'))?></div><?}?>
<?} else {?>
		<div class="cat_description">
			<p>К сожалению, на данный момент, мы не располагаем структурированным каталогом товаров для этого города. Для поиска интересующей Вас информации по другим городам воспользуйтесь выбором города.</p>
		</div>
<? }?>
		
	</div>
</div>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>
