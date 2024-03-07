<div class="popup wide">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?= $heading?></div>
		</div>
		<? if(isset($sub_heading)) {?><p class="text"><?=$sub_heading?></p><?}?>
		<div class="inputs">
			<? foreach ($fields as $field) {?>
				<?if($field['label'] !== '-') {?>
				<label><?= $field['label']?><?= $field['html']?></label>
				<?} else {?>
				<?= $field['html']?>
				<?}?>
			<? }?>
		</div>
		<div class="error-submit"><?if(isset($errors)){?>
			<?  foreach ($errors as $error) {?>
			<p><?=$error['message']?></p>
			<?}?>
		<?}?></div>
		<?= $controls['submit']['html']?>
	</form>
</div>