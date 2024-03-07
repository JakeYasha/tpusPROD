<div class="search_result" style="border-top: none;">
	<div class="attention-info">
		<p>Обратите внимание, что в разделе "Акции" Вы можете размещать информацию о следующих предложениях:</p>
		<p>	*	Скидки в процентах от цены на товары, услуги компании<br />
			*	Скидки в денежном выражении с указанием первоначальной цены на товар, услугу<br />
			*	Подарки при покупке, включая бесплатное оказание услуг</p>
		<p>Не относится к акциям предложение по выдаче "дисконтных карт" за покупку или "скидка на следующую покупку". Если Ваше предложение не относится к выше указанным категориям по размещению акции или не соответствует закону О рекламе, модератор сайта может его удалить.</p>
	</div>
</div>



<div class="popup wide">
	<div class="top_field">
		<div class="title">Изображение</div>
	</div>
	<div class="inputs">
		<div class="firm_field">
			<div class="image_field">
				<div class="image"><span><img class="js-upload-attach-holder" src="<?=$image_url?>" /></span></div>
			</div>
		</div>
		<?=app()->chunk()->set('url', '/firm-user/ajax/upload/firm-promo-image/')->set('label','Загрузить')->render('elem.file_upload_button')?>
	<div class="attention-info">
		<p>Загрузите изображение для акции в любом из форматов: JPG, GIF, PNG. Рекомендуемый формат 16:9, размер файла не более 2 Мб.</p>
	</div>
	</div>
</div>
<div class="popup wide">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?=$mode === 'edit' ? 'Изменение акции' : 'Добавление новой акции'?></div>
		</div>
		<div class="inputs">
			<?if($rubrics){?>
				<div class="attention-info">
					<p>При размещение акции выберите 3 подходящие к теме акции рубрики каталога товаров и услуг и мы дополнительно на страницах этих рубрик будем показывать анонсы ваших акций. Этим Вы сможете заинтересовать более широкий круг потенциальных покупателей.</p>
					<p>Заполняя информацию по акциям старайтесь все специальные предложения по одному ряду услуг размещать в одной акции. Так вероятность того, что посетитель заинтересуется именно вашей фирмой возрастет. Мы не рекомендуем размещать более 2 акций по одной и той же рубрике каталога.</p>
				</div>
				<label style="max-width: 90%;">Выберите рубрики для показа акции
				<div class="e-check-boxes">
					<ul>
						<?foreach($rubrics as $id => $row) {?>
							<li><label><input type="checkbox"<?if(in_array($id, $active_rubrics)){?> checked="checked"<?}?> class="js-rubric-selector" data-entity-type="FirmPromo" value="<?=$id?>" /><?=$row['name']?></label></li>
						<?}?>
					</ul>
				</div>
				</label>
			<?}?>
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
