<?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
?>

<? if (in_array(app()->location()->currentId(), ['37000'])) {?>
    <?= app()->chunk()->render('adv.header_banners')?>
    <br/>
<? } else if (app()->location()->stats('count_goods') > 75000 || (APP_IS_DEV_MODE && app()->location()->currentId() == '64043') || (app()->location()->currentId() == 60000)) {?>
    <div class="index_rubrics">
        <ul>
            <li>
                <a href="<?= app()->link('/catalog/')?>">
                    <img src="/img/icons_index_2.png" alt="Каталог предложений товаров<?= app()->location()->currentName('genitive')?>" />
                </a>
            </li>
            <li>
                <a href="<?= app()->link('/catalog/44/')?>">
                    <img src="/img/icons_index_1.png" alt="Каталог предложений услуг<?= app()->location()->currentName('prepositional')?>" />
                </a>
            </li>
            <? if (app()->location()->currentId() == 60000) {?>
            <li>
                <a target="_blank" href="https://sites.google.com/yandex.ru/news-spravka053ru/%D0%B3%D0%BB%D0%B0%D0%B2%D0%BD%D0%B0%D1%8F/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8">
                    <img src="/img/500x160_news.png" alt="Каталог предложений оборудования<?= app()->location()->currentName('prepositional')?>" />
                </a>
            </li>
            <?} else {?>
            <li>
                <a href="<?= app()->link('/catalog/22/')?>">
                    <img src="/img/icons_index_3.png" alt="Каталог предложений оборудования<?= app()->location()->currentName('prepositional')?>" />
                </a>
            </li>
            <?}?>
            <li>
                <a href="<?= app()->link('/advert-module/')?>">
                    <img src="/uploaded/1/d/1d9f02731fc7c3b56d605f3355482b59.jpg" alt="Акции и скидки<?= app()->location()->currentName('genitive')?>" />
                </a>
            </li>
        </ul>
    </div>
<? } else {?>
	<? /* <div class="main_banner">
	  <?= $big_mama_banner?>
	  </div> */?>
    <?= app()->chunk()->render('adv.header_banners')?>
    <br/>
<? }?>
<?= $rubrics?>
<div class="services">
	<?= $text_bottom->val('text')?>
</div>
<? /*if ($rss_news != "") {?>
	<div class="rss-news-block">
		<div class="rss-last-news-block">
			<h2>Новости Костромского региона</h2>
			<?= $rss_news?>
		</div>
	</div>
<? }*/?>
<div class="reviews">
	<?= $last_reviews?>
	<?= $new_companies?>
</div>
<?= $promo?>
<? if (app()->isNewTheme()) {
	if (isset($advert_modules)) {
		?><?= $advert_modules?><? }
}
?>
<div class="services">
<?= $text_bottom_by_location->val('text')?>
</div>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
		
<div class="pre_footer_adv_block">
		<?=app()->chunk()->render('adv.bottom_banners')?>
</div>

<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
?>