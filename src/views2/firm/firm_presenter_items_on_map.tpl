<div class="js-map-points hidden">
	<? /* @var $items Firm[] */?>
	<? /* @var $coords FirmCoords[] */?>
	<?
	$coords = $items['coords'] ? $items['coords'] : [];
	$items = $items['items'];
	?>
	<?
	$i = 0;
	$cnt = count($items);
	foreach ($items as $item) {
		$i++;
		?>
		<?if($item->hasAddress()){?>
	<div class="js-map-points-coord hidden" data-id="<?=$item->id()?>" data-name="<?=  htmlspecialchars($item->name())?>" <? if ($coords[$item->id()]) {?> data-coords-lat="<?= $coords[$item->id()]['lat']?>" data-coords-lng="<?= $coords[$item->id()]['lng']?>"<? } else {?> data-address="<?= $item->address()?>"<? }?>>
				<div class="popup_in_map">
					<div class="manuf_field">
					<div class="man_desc">
					<a class="name_firm" href="<?=$item->link()?>"><?=$item->name()?></a>
					<p><?=$item->val('company_activity')?></p>
					</div>
					<div class="contacts">
						<div class="title">контакты:</div>
						<p><span class="r tel"><?=$item->phone()?></span></p>
						<p><?=$item->shortAddress()?></p>
						<?=($item->hasWeb() ? '<p><span class="r"><a target="_blank" href="'.app()->away($item->webSiteMain(), $item->id_firm()).'" rel="nofollow">'.$item->webSiteMain().'</a></span></p>' : '')?>
					</div>
					</div>
				</div>
			</div>
		<?}?>
<? }?>
</div>
<div class="map_field on_map">
	<div id="map"></div>
</div>