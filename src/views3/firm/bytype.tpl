<div class="mdc-layout-grid">
    <?= $bread_crumbs?>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <div class="block-title">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading block-title__heading_h1"><?= $firm_type['title']?></h1>
        <p><?=$header?></p>
    </div>
	<?if($firm_type['text'] && app()->location()->stats('count_goods') > 100000){?>
		<p><?= $firm_type['text']?></p>
	<?}?>

	<?= app()->chunk()->set('items', $sub_items)->set('filter', [])->render('common.catalog_tags')?>
    <br/>
	<div class="pre_footer_adv_block">
		<?=app()->chunk()->render('adv.catalog_top_banners')?>
	</div>
	<div class="search-result">
		<?=$tabs?>
		<div class="search-result-content">
			<?= $items?>
			<div class="delimiter-block"></div>				
			<?= $pagination?>
			<br/>
			<div class="bot_text">
				<?= $firm_type['text_bottom']?>
			</div>
		</div>
	</div>
    <br/>
	<div class="pre_footer_adv_block">
		<?= app()->chunk()->render('adv.bottom_banners')?>
	</div>
	
    <?=$advert_restrictions?>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>