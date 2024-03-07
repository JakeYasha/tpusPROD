<div class="popup wide">
	<form<?=html()->renderAttrs($attrs)?>>
	<div class="top_field">
		<div class="title"><?=$heading?></div>
	</div>
	<p class="text"><?=$sub_heading?></p>
	<div class="inputs">
	<?foreach ($fields as $field) {?>
		<?if($field['elem'] !== 'hidden_field'){?>
		<label style="max-width: 90%;"><?=$field['label']?><?=$field['html']?></label>
		<?} else {?>
		<?=$field['html']?>
		<?}?>
	<?}?>
	</div>
	<div class="error-submit"></div>
	<?=$controls['submit']['html']?>
	</form>
</div>