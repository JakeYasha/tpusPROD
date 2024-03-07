<div class="popup wide" style="width: 900px; height: 500px;">
	<div class="top_field">
		<div class="title">ФОТО</div>
	</div>
	<div class="inputs" style="padding-left: 25px;">
		<div class="firm_field" style="width: 213px;">
			<?if($item['image']){?>
			<div class="image_field">
				<div class="image" style="margin-left: 0;"><span><img class="js-upload-attach-holder" src="<?= $item['image']?>" /></span></div>
			</div>
			<?} else {?>
			<div class="image_field">
				<div class="image" style="margin-left: 0;"><span><img class="js-upload-attach-holder" src="/img/no_img.png" /></span></div>
			</div>
			<?}?>
		</div>
		<div class="price-images-dialog search_result">
			<?if($images){?>
			<ul>
				<?foreach($images as $img) {?>
				<li class="search_result_cell"><div class="image"><a title="Выбрать" href="/firm-user/ajax/set-price-image/?id_image=<?=$img->id()?>&id_price=<?=$id_price?>" class="js-action js-add-image-to-price button"><img style="width: " src="<?=$img->path()?>" /></a></div></li>
				<?}?>
			</ul>
			<?}?>
		</div>
		<?= app()->chunk()->set('url', '/firm-user/ajax/upload/price-image/?id_firm=' . $id_firm .'&id_price='.$id_price)->set('label', $item['image'] ? 'Изменить' : 'Загрузить')->render('elem.file_upload_button')?>
		<?if($item['image']){?><a href="#" data-id="<?=$item['image_id']?>" data-id-price="<?=$id_price?>" class="js-action js-remove-image button" style="padding: 10px 0 0 15px;display: block;">удалить фотографию</a><?}?>
	</div>
</div>