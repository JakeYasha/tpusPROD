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
<? if ($rubrics !== '') {?>
	<?= $rubrics?>
<? } else {?>
	<? /* <div class="main_banner">
	  <?= $big_mama_banner?>
	  </div> */?>
<? }?>

<div class="services">
	<?= $text_bottom->val('text')?>
</div>
<? if ($rss_news != "") {?>
	<div class="rss-news-block">
		<div class="rss-last-news-block">
			<h2>Новости Костромского региона</h2>
			<?= $rss_news?>
		</div>
	</div>
<? }?>
<div class="reviews">
	<?= $last_reviews?>
	<?= $new_companies?>
</div>
<?= $promo?>
<? if ($_SERVER['REMOTE_ADDR'] == '93.158.228.86' || $_SERVER['REMOTE_ADDR'] == '93.181.225.108') {
	if (isset($advert_modules)) {
		?><?= $advert_modules?><? }
}
?>
<div class="services">
<?= $text_bottom_by_location->val('text')?>
</div>