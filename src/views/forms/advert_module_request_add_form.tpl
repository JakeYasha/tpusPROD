<div class="popup">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?= $heading?></div>
		</div>
		<div class="inputs">
			<?  foreach ($hidden_fields as $key=>$val) {?>
			<input type="hidden" name="<?=$key?>" value="<?=$val?>" />
			<?}?>
			<? foreach ($fields as $field) {?>
				<label><?= $field['label']?><?= $field['html']?></label>
			<? }?>
		</div>
		<?= app()->capcha()->render()?>
		<?= $controls['submit']['html']?>
	</form>
</div>