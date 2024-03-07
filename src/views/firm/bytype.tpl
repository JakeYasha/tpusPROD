<?= $bread_crumbs?>
	<div class="cat_description">
		<h1><?= $firm_type['title']?></h1>
		<p><?=$header?></p>
	</div>
	<?if($firm_type['text'] && app()->location()->stats('count_goods') > 100000){?>
		<div class="firm-type-text">
			<?= $firm_type['text']?>
		</div>
	<?}?>

	<?= app()->chunk()->set('items', $sub_items)->set('filter', [])->render('common.catalog_tags')?>
	<?= app()->chunk()->render('adv.catalog_top_banners')?>
	<div class="search_result">
		<?=$tabs?>
		<div class="search_result_content">
			<?= $items?>
			<?= $pagination?>
			<br/>
			<div class="bot_text">
				<?= $firm_type['text_bottom']?>
			</div>
		</div>
	</div>
<div class="pre_footer_adv_block">
	<?= app()->chunk()->render('adv.bottom_banners')?>
</div>
<?=$advert_restrictions?>