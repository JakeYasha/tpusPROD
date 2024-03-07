<?= $bread_crumbs?>
<div class="cat_description">
    <h1><?= app()->metadata()->getHeader()?></h1>
	<?= $text?>
    <div class="hp_clear"></div>
</div>
<div class="item_info">
	<?= app()->chunk()->set('filter', $filters)->set('items', $tags)->set('link', app()->link('/advert-module/'))->render('common.catalog_am_tags')?>
	<div class="hp_coupon_block">
		<?= $items?>
		<?= $pagination?>
	</div>	
	<div style="clear: both"></div>
	<?= $promo?>
	<div class="cat_description" style="clear: both;">
		<div class="notice-dark-grey">
			<h2 style="text-align: center">Хотите разместить здесь свои предложения и акции?</h2>
			<p style="text-align: center;">Создайте заявку на размещение информации на сайте tovaryplus.ru и наш менеджер свяжется с вами для уточнения деталей заявки. </p>
			<div style="text-align: center;"><a class="button_red" href="/request/add/"> Создать заявку </a></div>
		</div>
	</div>
	<div class="for_clients_text_c advert_wrapper clearfix page">
	</div>
</div>
<?=
$advert_restrictions?>