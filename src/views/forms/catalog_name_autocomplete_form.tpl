<form<?= html()->renderAttrs($attrs)?>>
	<div class="top_field">
		<div class="title">Поиск названия категории</div>
	</div>
	<div class="inputs">
		<? foreach ($fields as $field) {?>
			<label><?= $field['label']?><?= $field['html']?></label>
		<? }?>
	</div>
	<div class="error-submit"></div>
	<?= $controls['submit']['html']?>
</form>