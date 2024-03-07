<? /* @var $firm Firm */?>
<div class="popup">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?= $heading?></div>
		</div>
		<div class="inputs">
			<input type="hidden" name="id_firm" value="<?=$firm->id()?>" />
			<input type="hidden" name="referer" value="<?=$referer?>" />
			<? foreach ($fields as $field) {?>
				<label><?= $field['label']?><?= $field['html']?></label>
			<? }?>
		</div>
		<?= app()->capcha()->render()?>
		<div class="error-submit" style="height: 40px;"></div>
		<?= $controls['submit']['html']?>
	</form>
</div>