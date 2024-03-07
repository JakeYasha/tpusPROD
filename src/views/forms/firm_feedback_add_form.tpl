<? /* @var $firm Firm */?>
<div class="popup">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?= $heading?></div>
		</div>
		<div class="inputs">
			<?if(!$feedback_option->exists()) {?>
			<label>Кому<input type="text" value="<?=$firm->name()?>, &lt;<?=$firm->firstEmail()?>&gt;" readonly="readonly" disabled="disabled" /></label>
			<?}?>
			<input type="hidden" name="id_firm" value="<?=$firm->id()?>" />
			<input type="hidden" name="feedback_option" value="<?=$feedback_option->id()?>" />
			<input type="hidden" name="request_uri" value="<?=$_SERVER['HTTP_REFERER']?>" />
			<? foreach ($fields as $field) {?>
				<label><?= $field['label']?><?= $field['html']?></label>
			<? }?>
            <?= app()->chunk()->render('common.agreement_block') ?>
		</div>
		<?= app()->capcha()->render()?>
		<div class="error-submit" style="height: 40px;"></div>
		<?= $controls['submit']['html']?>
	</form>
</div>