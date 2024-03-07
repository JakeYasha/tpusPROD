<div class="popup wide">
	<div class="top_field">
		<div class="title">Общая информация о фирме</div>
	</div>
	<div class="inputs">
		<div class="attention-info">
			<p>Здесь вы можете изменить информацию о фирме размещенную на сайте. После того как Вы нажмете кнопку "Сохранить", будет сформирована заявка на редактирование и отправлена модератору базы данных. Если модератор подтвердит изменения, информация появится на сайте. Если измененные вами поля подсвечены цветом, значит информация еще не обновилась.</p>
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