<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page">
		<h2><?= $item['name']?></h2>
		<? if ($item['big_image']) {?>
		<p><img style="max-width: 750px; margin: 0 auto; display: inherit; width: 100%;" src="<?= $item['big_image']?>" alt="<?=encode($item['name'])?>" /></p>
		<? }?>
		<div><?= $item['big_text']?></div>
		<div class="notice-dark-grey">
			<p><strong>Срок действия: </strong> <span class="org infinite"><?= $item['flag_is_infinite'] ? 'Постоянная акция' : 'с ' . $item['time_beginning'] . ' по ' . $item['time_ending']?></span></p>
			<p><strong>Контактный телефон: </strong> <span class="org infinite"><?=$item['phone']?></span></p>
		</div>  
	</div>
	<?=$another_items?>
</div>