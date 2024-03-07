<div class="popup wide">
	<div class="top_field">
		<div class="title">Доставка</div>
	</div>
	<div class="inputs">
		<div class="attention-info">
			<p>Здесь вы можете указать информацию о возможных вариантах доставки и оплаты ваших товаров. Информация о способах доставки будет показана на сайте вашей компании в разделе «Условия доставки» и в описании ваших товаров по ссылке «Условия доставки». Список возможных вариантов также отобразится в форме заказа, чтобы покупатель смог выбрать наиболее удобный из них.</p>
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
<div class="delimiter-block"></div>