<form action="<?=$url?>" enctype="multipart/form-data" data-view-ver="1">
	<input class="<?if(isset($jsclass)){?><?=$jsclass?><?} else {?>js-upload-attach-file-input<?}?>" accept="image/*" name="<?if(isset($name)){?><?=$name?><?} else {?>files<?}?>" type="file" style="width: 172px; height: 36px; opacity: 0.01;cursor:pointer;" />
	<button class="e-button send js-upload-button" type="button" style="float: none; margin: 0; width: 172px; margin-top: -36px;"><?=isset($label) ? $label : 'Загрузить новый'?></button>
</form>