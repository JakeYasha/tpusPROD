<div class="popup wide">
	<div class="top_field">
		<div class="title">Общая информация о филиале</div>
	</div>
	<div class="inputs">
		<div class="attention-info">
			<p>Здесь вы можете изменить информацию о филиале размещенную на сайте. Значения полей по умолчанию были взяты из основной фирмы Личного кабинета.</p>
		</div>	
	</div>	
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="inputs">
			<? foreach ($fields as $field) {?>
				<? if ($field['elem'] !== 'hidden_field') {?>
					<label style="max-width: 90%;"><?= $field['label']?><?= $field['html']?></label>
				<? } else {?>
					<?= $field['html']?>
				<? }?>
			<? }?>
		</div>
		<div class="error-submit"></div>
		<?= $controls['submit']['html']?>
	</form>
</div>