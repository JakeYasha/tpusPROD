<div class="delimiter-block"></div>
<div class="popup wide">
	<div class="top_field">
		<div class="title">Фотогалерея</div>
	</div>
	<div class="delimiter-block"></div>
	<div class="inputs">
		<div class="js-upload-attach-holder-images upload-attach-holder-images js-firm-image-mode"><? foreach ($price_images as $img) {?><div class="firm-image"><img src="<?= $img->thumb()?>" /><a href="#" class="js-action js-remove-price-image img-del-btn" data-id="<?= $img->id()?>"></a></div><? }?></div>
		<?= app()->chunk()->set('url', '/firm-user/ajax/upload/price-images/?id_firm=' . $id_firm . '&id_service=' . $id_service . '&id_price=' . $model->id())->set('label', 'Загрузить фотографии')->render('elem.files_upload_button')?>
		<div class="attention-info">
			<p>Здесь Вы можете загрузить фотографии товара или услуги в одном из предложенных форматов: JPG, GIF, PNG. Рекомендуемые размеры изображения &mdash; не менее 160х160px. При загрузке изображение будет оптимизировано под рекомендуемый размер. Вы можете загрузить до 10 фотографий одного товара/услуги.</p>
		</div>
	</div>
</div>
<div class="delimiter-block"></div>
<div class="popup wide">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?=$heading?></div>
		</div>
		<div class="text">
			<?= app()->config()->get('firmuser.price_add.top_text')?>
		</div>
		<div class="inputs inputs-price-form">
			<? if ($type === 'goods' || $type === 'equipment') {?>
				<label><?= $fields['name']['label']?><?= $fields['name']['html']?></label>
				<label><?= $fields['description']['label']?><?= $fields['description']['html']?></label>
				<label class="w25"><?= $fields['price']['label']?><?= $fields['price']['html']?></label>
				<label class="w25"><?= $fields['price_wholesale']['label']?><?= $fields['price_wholesale']['html']?></label>
				<label class="w25"><?= $fields['price_old']['label']?><?= $fields['price_old']['html']?></label>
				<label class="w25" style="padding-right: 45px;"><?= $fields['price_wholesale_old']['label']?><?= $fields['price_wholesale_old']['html']?></label>
				<label class="w25 search_field"><?= $fields['unit']['label']?><?= $fields['unit']['html']?></label>
				<label><?= $fields['flag_is_available']['label']?><?= $fields['flag_is_available']['html']?></label>
				<label><?= $fields['flag_is_delivery']['label']?><?= $fields['flag_is_delivery']['html']?></label>

				<label><?= $fields['country_of_origin']['label']?><?= $fields['country_of_origin']['html']?></label>
				<label><?= $fields['vendor']['label']?><?= $fields['vendor']['html']?></label>
			<? } else {?>
				<label><?= $fields['name']['label']?><?= $fields['name']['html']?></label>
				<label><?= $fields['info']['label']?><?= $fields['info']['html']?></label>
				<label class="w25 search_field"><?= $fields['unit']['label']?><?= $fields['unit']['html']?></label>
				<label class="w25"><?= $fields['price']['label']?><?= $fields['price']['html']?></label>
				<label class="w25"><?= $fields['price_old']['label']?><?= $fields['price_old']['html']?></label>
			<? }?>
		</div>
		<div class="error-submit"></div>
		<?= $fields['id_catalog']['html']?>
		<?= $fields['id_group']['html']?>
		<?= $fields['id_subgroup']['html']?>
		<?= $fields['id']['html'] ?? ''?>
		<?= $controls['submit']['html']?>
	</form>
</div>