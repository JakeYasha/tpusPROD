<div class="popup wide">
	<div class="top_field">
		<div class="title">Логотип</div>
	</div>
	<div class="inputs">
		<div class="firm_field">
			<div class="image_field">
				<div class="image"><span><img class="js-upload-attach-holder" src="<?= $logo_path?>" /></span></div>
			</div>
		</div>
		<?=app()->chunk()->set('url', '/firm-user/ajax/upload/firm-logo/?id_firm='.$id_firm)->render('elem.file_upload_button')?>
		<a href="/firm-user/restore-default-logo/" class="js-default-logo-btn default-logo-btn button<?if(!$has_default_logo){?> hidden<?}?>">или вернуть логотип по-умолчанию</a>
	<div class="attention-info">
		<p>Здесь Вы можете загрузить логотип вашей компании, торговой марки или бренда в одном из предложенных форматов: JPG, GIF, PNG. Рекомендуемые размеры изображения 160х160px. При загрузке изображение будет оптимизировано под рекомендуемый размер. Чтобы удалить изображение, воспользуйтесь ссылкой "вернуть логотип по-умолчанию".</p>
	</div>
	</div>
</div>
<div class="delimiter-block"></div>
<div class="popup wide">
	<div class="top_field">
		<div class="title">Описание</div>
	</div>
	<form<?=html()->renderAttrs($attrs)?>>
	<div class="inputs">
	<?foreach ($fields as $field) {?>
		<?if($field['elem'] !== 'hidden_field'){?>
		<label style="max-width: 90%;"><?=$field['label']?><?=$field['html']?></label>
		<?} else {?>
		<?=$field['html']?>
		<?}?>
	<?}?>
	<div class="attention-info">
		<p>Чтобы у посетителя было полное представление о вашей компании, укажите в описании общую, но точную и актуальную информацию о вашей деятельности, товарах, услугах, клиентах, конкурентных преимуществах, истории вашей компании и т.д. Рекомендуем вам кратко описать основные сферы деятельности вашей компании, ее историю и достижения. Внимательно проверьте информацию перед её сохранением на отсутствие ошибок в тексте. Вы можете использовать панель форматирования текста для его оформления и придания описанию наиболее представительного вида.</p>
	</div>
	</div>
	<div class="error-submit"></div>
	<?=$controls['submit']['html']?>
	</form>
</div>
<div class="delimiter-block"></div>
<div class="popup wide">
	<div class="top_field">
		<div class="title">Фотогалерея</div>
	</div>
	<div class="delimiter-block"></div>
	<div class="inputs">
		<div class="js-upload-attach-holder-images upload-attach-holder-images js-firm-image-mode"><?foreach($firm_images as $img){?><div class="firm-image"><img src="<?=$img->thumb()?>" /><a href="#" class="js-action js-remove-firm-file img-del-btn" data-id="<?=$img->id()?>"></a></div><?}?></div>
		<?=app()->chunk()->set('url', '/firm-user/ajax/upload/firm-images/?id_firm='.$id_firm)->render('elem.files_upload_button')?>
		<div class="attention-info">
			<p>Добавьте серию фотографий для презентации вашей компании. Это могут быть фотографии вашего офиса или производства, портфолио ваших работ или фото ваших товаров. Совершая свой выбор, ваши клиенты обязательно будут руководствоваться также и этой визуальной информацией. Рекомендуемые размеры изображений для размещения в фотогаллерее 800х600px. При загрузке все изображения оптимизируются. Max размер - 1000px</p>
		</div>
	</div>
</div>
<div class="delimiter-block"></div>
