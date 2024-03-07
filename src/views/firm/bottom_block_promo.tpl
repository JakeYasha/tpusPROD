<div class="item_info">
	<div class="search_result">
		<?= $tabs?>
		<?if($items){?>
		<h2><?=app()->metadata()->getHeader()?></h2>
		<?$i=0;foreach ($items as $item) {$i++;
                        $id_firm = $item['id_firm'];
                        $id_service = $item['id_service'];
                        $text = $item['text'];
                        $text = str()->replace(str()->replace($text, ' target="_blank"', ''), ' rel="nofollow"', '');
                        $text = preg_replace_callback('~href="([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
                                return 'target="_blank" rel="nofollow" href="' . app()->away(trim($matches[1]), $id_firm) . '"';
                        }, $text);
                        ?>
			<div class="search_result_cell border-bottom-block top0"><span class="number"><?=$i?></span>
				<div class="image"><?if($item['flag_is_present']){?><div class="promo-value present"></div><?} elseif($item['percent_value']) {?><div class="promo-value"><?=$item['percent_value']?>%</div><?}?><a href="<?=$item['link']?>"><?if($item['image']){?><img src="<?=$item['image']?>" alt="<?=str()->replace($item['name'], ['"'], ['&quot;'])?>" /><?}?></a></div>
				<div class="title"><a href="<?=$item['link']?>"><?=$item['name']?></a></div>
				<div class="description description-promo">
					<div><?=$text?></div>
					<p class="promo-date-block"><span>Срок действия: </span> <?=$item['flag_is_infinite'] ? '<span class="promo-infinite">Постоянная акция</span>' : 'с '.$item['time_beginning'].' по '.$item['time_ending']?></p>
				</div>
			</div>
			<?}?>
		<?}?>
	</div>
</div>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>