<div class="popup wide">
	<div class="top_field">
		<div class="title">Схема проезда</div>
	</div>
	<div class="inputs">
		<div class="attention-info">
			<p>Здесь вы можете указать расположение Вашей организации на карте. Переместите метку на карте и нажмите кнопку Сохранить.</p>
		</div>
	</div>	
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="inputs">
			<? foreach ($fields as $field) {?>
				<? if ($field['elem'] !== 'hidden_field') {?>
					<label style="max-width: 90%;"><?= $field['label']?><?= $field['html']?></label>
				<? } else {?>
					<?= $field['html']?>
				<? }?>
			<? }?>
		</div>
                <div class="js-map-points hidden">
                        <div class="js-map-points-coord hidden" data-editable="1" data-id="<?=$firm->id()?>" data-name="<?=  htmlspecialchars($firm->name())?>" data-coords-lat-name="coords_latitude" data-coords-lng-name="coords_longitude" <? if ($firm_coords) {?> data-coords-lat="<?= $firm_coords->val('coords_latitude')?>" data-coords-id="<?= $firm_coords->id()?>" data-coords-lng="<?= $firm_coords->val('coords_longitude')?>"<? } else {?> data-address="<?= $firm->address()?>"<? }?> data-current="1">
                                <div class="popup_in_map">
                                        <div class="manuf_field">
                                        <div class="man_desc">
                                        <div class="name_firm_head"><?=$firm->name()?></div>
                                        <?if ($firm->val('company_activity') && strlen($firm->val('company_activity')) > 1) {?>
                                        <p><?=$firm->val('company_activity')?></p>
                                        <?}?>
                                        </div>
                                        <div class="contacts">
                                                <div class="title">контакты:</div>
                                                <p><span class="r tel"><?=$firm->phone()?></span></p>
                                        </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <div class="map_field">
                        <div id="map"></div>
                </div>
		<div class="error-submit"></div>
		<?= $controls['submit']['html']?>
	</form>
</div>
<div class="delimiter-block"></div>