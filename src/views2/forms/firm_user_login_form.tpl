<div class="popup js-ajax-form js-ajax-send-submit-enter">
	<form<?=html()->renderAttrs($attrs)?>>
	<div class="top_field">
		<div class="title"><?=$heading?></div>
	</div>
	<p class="text"><?=$sub_heading?></p>
	<div class="inputs">
	<?foreach ($fields as $field) {?>
		<label><?=$field['label']?><?=$field['html']?></label>
	<?}?>
		<label><a rel="nofollow" class="fancybox fancybox.ajax" href="/firm-user/get-restore-form/">Напомнить пароль</a></label>
	</div>
	<div class="error-submit"></div>
	<?=$controls['submit']['html']?>
	</form>
</div>