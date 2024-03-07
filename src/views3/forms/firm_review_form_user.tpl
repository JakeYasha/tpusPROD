<div class="popup wide">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title">Ответ на отзыв</div>
		</div>
		<div class="text">
			<p style="font-weight: bold;"><?=$item['user_name']?></p><p class="description"><?=$item['user_email']?></p><p class="description"><?=$item['datetime']?></p>
			<p><?=$item['text']?></p>
			<p><?= app()->chunk()->setArgs([$item['score'], true])->render('rating.stars')?></p>
			<div class="delimiter-line"></div>
		</div>
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