<? if ($items['items']) { ?>
<div class="js-map-points hidden">
	<? /* @var $items Firm[] */?>
	<? /* @var $coords FirmCoords[] */?>
	<?
	$coords = $items['coords'];
	$current = isset($items['current_firm']) ? $items['current_firm'] : false;
	$items = $items['items'];
    ?>
	<?
	$i = 0;
	$cnt = count($items);
	foreach ($items as $item) {
		$i++;
		?>
		<?if($item->hasAddress()){?>
            <div class="js-map-points-coord hidden" data-id="<?=$item->id()?>" data-name="<?=  htmlspecialchars($item->name())?>" <? if ($coords[$item->id()]) {?> data-coords-lat="<?= $coords[$item->id()]['lat']?>" data-coords-id="<?= $coords[$item->id()]['id'] ?? ''?>" data-coords-lng="<?= $coords[$item->id()]['lng']?>"<? } else {?> data-address="<?= $item->address()?>"<? }?><?if($current === $item->id()){?> data-current="1"<?}?>>
				<div class="popup_in_map">
					<div class="manuf_field">
					<div class="man_desc">
					<div class="name_firm_head"><?=$item->name()?></div>
                    <?if ((isset($params['show_company_activity']) && $params['show_company_activity']) && $item->val('company_activity') && strlen($item->val('company_activity')) > 1) {?>
                        <p><?=$item->val('company_activity')?></p>
                    <?}?>
					</div>
					<div class="contacts">
						<div class="title">контакты:</div>
						<p><span class="r tel"><?=$item->phone()?></span></p>
                        <p><?=$item->shortAddress()?></p>
					</div>
					</div>
				</div>
			</div>
		<?}?>
    <? }?>
</div>
<?}?>