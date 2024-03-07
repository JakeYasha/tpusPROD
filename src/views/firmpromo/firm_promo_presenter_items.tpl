<? if ($items) {?>
	<? $i = 0;
	foreach ($items as $item) {
		$i++;?>
		<div class="search_result_cell border-bottom-block"><span class="number"><?= $i?></span>
			<div class="image" style="position: relative;"><?if($item['flag_is_present']){?><div class="promo-value present"></div><?} elseif($item['percent_value']) {?><div class="promo-value"><?=$item['percent_value']?>%</div><?}?><a href="<?= $item['link']?>"><? if ($item['image']) {?><img src="<?= $item['image']?>" alt="<?= encode($item['name'])?>" /><? }?></a></div>
			<div class="title"><a href="<?= $item['link']?>"><?= $item['name']?></a></div>
			<div class="description description-promo">
				<div><?= $item['text']?></div>
				<div class="org">
					<p>Срок действия: <span class="infinite"><?= $item['flag_is_infinite'] ? 'Постоянная акция' : 'с ' . $item['time_beginning'] . ' по ' . $item['time_ending']?></span></p>
					<?$firm = $item['firm']?>
					<p>Телефон для справок: <?=$item['phone']?><br /><a href="<?= $firm->linkItem()?>"><?= $firm->name()?></a></p>
				</div>
			</div>	
		</div>
	<? }?>
<?
}?>