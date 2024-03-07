<div class="popup js-ajax-send-submit-enter">
	<form<?=html()->renderAttrs($attrs)?>>
	<div class="top_field">
		<div class="title"><?=$heading?></div>
	</div>
	<?if($has_last_logon_timestamp){?>
		<p class="text">Последний заход: <?=$last_logon_timestamp?></p>
	<?} else {?>
                <?if($has_firm_user){?>
                    <p class="text">Фирма ещё не заходила в личный кабинет</p>
                <?} else {?>
                    <p class="text">Для фирмы личный кабинет ещё не был создан</p>
                <?}?>
	<?}?>
	<div class="inputs">
	<?foreach ($fields as $k=>$field) {?>
		<? if ($field['elem'] !== 'hidden_field') {?>
			<label><?= $field['label']?><?= $field['html']?></label>
		<? } else {?>
			<?= $field['html']?>
		<? }?>
	<?}?>
		<?if($has_firm_user){?><label><a class="js-action js-send-new-password-link" href="/firm-manager/restore-password/?id_firm_user=<?=$id_firm_user?>&id_firm=<?=$id_firm?>">Отправить новый пароль</a></label><?}?>
	</div>
	<div class="error-submit"></div>
	<?=$controls['submit']['html']?>
	</form>
</div>