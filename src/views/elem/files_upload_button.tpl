<form action="<?= $url?>" enctype="multipart/form-data" class="js-form-upload-files">
	<input class="js-upload-attach-files-input" accept="<?= isset($accept) ? $accept : 'image/*'?>" multiple="multiple" name="files" type="file" style="width: 172px; height: 36px; opacity: 0.01;cursor:pointer;" />
	<button class="e-button send js-upload-button" type="button" style="float: none; margin: 0; width: 172px; margin-top: -36px;">Загрузить файлы</button>
</form>