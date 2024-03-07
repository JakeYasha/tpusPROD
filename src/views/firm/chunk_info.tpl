<div class="org">
	<p class="head">Фирма: </p>
	<?/*<p><?= $firm->phone()?></p>*/?>
	<?/*<a href="<?= $firm->linkItem()?>"><?= $firm->name()?></a>*/?>
	<p><?= $firm->name()?></p>
	<p><?=app()->chunk()->set('item', $firm)->render('firm.chunk_phone')?></p>
	<?/*<span class="rev">15 отзывов<sup>todo</sup></span>
	<span class="map">На карте</span>*/?>
</div>