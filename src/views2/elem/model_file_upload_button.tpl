<form action="<?=$url?>" enctype="multipart/form-data">
	<input class="<?if(isset($jsclass)){?><?=$jsclass?><?} else {?>js-upload-attach-file-input<?}?>" accept="image/*" name="<?if(isset($name)){?><?=$name?><?} else {?>files<?}?>" type="file" style="width: 172px; height: 36px; opacity: 0.01; cursor:pointer;" />
    <input name="model_id" type="hidden" value="<?if(isset($model_id)){?><?=$model_id?><?} else {?>0<?}?>" />
	<button class="e-button send js-upload-button" type="button" style="float: none; margin: 0; width: 172px; margin-top: -36px;"><?=isset($label) ? $label : 'Загрузить новый'?></button>
</form>