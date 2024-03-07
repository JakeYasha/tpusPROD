<form<?=html()->renderAttrs($attrs)?>>
<h2><strong>2 Шаг</strong>. Как Вас зовут?</h2>
<div class="inputs">
	<label><?=$fields['user_name']['html']?></label>
</div>
<h2><strong>3 Шаг</strong>. Оставьте свой номер мобильного телефона.</h2>
<div class="inputs">
	<label><?=$fields['user_phone']['html']?></label>
</div>
<p>(Эти данные нигде не будут использованы)</p>
<p>ПОБЕДИТЬ в конкурсе вы можете один раз в ТРИ месяца. Спасибо за понимание, мы экономим ваше время!</p>
<h2><strong>4 Шаг</strong>. Выберете категорию, в которой хотите участвовать.</h2>
<div class="inputs e-check-boxes">
	<?foreach ($nominations as $nom) {?>
	<label><?=$nom['name']?><input <?if($filters['nomination'] === (int)$nom['id']){?> checked="checked"<?}?> type="radio" name="id_nomination" class="e-check-box grey" value="<?=$nom['id']?>"></label>
	<?}?>
</div>
<input type="hidden" name="id_contest" value="<?=$item['id']?>" />
<div class="error-submit"></div>
<?=$controls['submit']['html']?>
</form>