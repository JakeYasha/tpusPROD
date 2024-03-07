<div class="popup js-ajax-send-submit-enter">
	<form<?=html()->renderAttrs($attrs)?>>
	<div class="top_field">
		<div class="title"><?=$heading?></div>
	</div>
	<div class="inputs">
	<?foreach ($fields as $k=>$field) {?>
		<? if ($field['elem'] !== 'hidden_field') {?>
			<label><?= $field['label']?><?= $field['html']?></label>
		<? } else {?>
			<?= $field['html']?>
		<? }?>
	<?}?>
	</div>
	<div class="error-submit"></div>
	<?=$controls['submit']['html']?>
	</form>
</div>