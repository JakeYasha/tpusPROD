<div class="rubrics js-catalog-rubrics-wrapper">
	<div class="rubrics-left">
		<ul class="js-catalog-rubrics">
			<?$i=-1;foreach($rubrics as $rubric){$i++;if(!isset($items[$i]))continue;?>
			<li data-index="<?=$i?>" class="js-catalog-rubrics-li"><i class="spr-rub spr-rub-<?=$rubric->val('css_class')?>"></i><?=$rubric->name()?></li>
			<?}?>
		</ul>
	</div>
	<?=  app()->chunk()->render('adv.catalog_rubrics_banners')?>
	<?foreach ($items as $index => $level1){if(!$level1)continue;?>
	<div class="rubrics-right js-rubrics-right-blocks js-rubrics-right-block-<?=$index?>">
		<ul class="parent">
			<?  foreach ($level1 as $id=>$val) {$sub = $val['item'];?>
			<li>
				<ul>
					<li class="top"><a href="<?=$sub->link() != '#' ? app()->link($sub->link()) : '#'?>"<?=$sub->link() == '#' ? ' class ="js-block-event"' : ''?>><?=$sub->name()?></a></li>
					<?foreach($val['subs'] as $k=>$child){?>
						<li><a href="<?=app()->link($child->link())?>"><?=$child->name()?></a></li>				
					<?}?>
				</ul>
			</li>
			<?}?>
		</ul>
	</div>
	<?}?>
</div>