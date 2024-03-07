<?= app()->breadCrumbs()->render()?>
<div class="black-block">Загрузка YML-файла</div>
<div class="item_info">
	<div class="search_result">		
		<div class="popup wide">
			<div class="inputs" style="padding-top: 20px;">
				<? if ($items) {?>
					<table class="default-table pages-table">
						<tr>
							<th>Дата обновления файла</th>
							<th>Тип</th>
							<th>Загружено/Всего</th>
							<th>Статус</th>
							<th>Формат названия</th>
							<th>Переход по ссылке</th>
							<th>Действия</th>
						</tr>
						<? foreach ($items as $item) {?>
							<tr>
								<td><?= $item['timestamp_yml']?></td>
								<td><?= $item['type']?></td>
								<td><?= $item['offers_count']?>/<?= $item['offers_count_loaded']?></td>
								<td><?= $item['status']?></td>
								<td><?= $item['name_format']?></td>
								<td><?= $item['flag_is_referral']?></td>
								<td><a href="#" class="js-action js-open-yml-log" data-id="<?=$item['id']?>">История</a><br/><a href="<?=$item['url']?>" target="_blank">Yml-файл</a></td>
							</tr>
							<? foreach ($item['logs'] as $log) {?>
							<tr class="tr-yml-log tr-yml-log-<?=$item['id']?>">
								<td colspan="7">Старт обновления <?= $log['timestamp_inserting']?>. Завершено в <?=$log['timestamp_last_updating']?>. Загружено <?=$log['offers_count']?>. На сайте <?=$log['offers_count_active']?></td>
							</tr>
							<?}?>
						<? }?>
					</table>
				<? }?>
			</div>
		</div>
	</div>
	<div class="search_result">
		<div class="attention-info">
			<p>Вы можете загрузить каталог своих предложений на сайт tovaryplus.ru в формате Яндекс.Маркета. <p/>
			<p>Для успешного импорта файл с товарами должен соответствовать формату YML, XML. Подробнее о данных форматах можно почитать в <a href="https://yandex.ru/support/partnermarket/export/yml.html">инструкции</a></p>  
			<p>После процедуры импорта, наш модератор настроит соответствие рубрик передаваемых в файле YML c рубрикатором каталога товаров и услуг и опубликует предложения на сайте. В случае, когда загрузка была сделана по ссылке к файлу, мы будем проверять обновления этого файла и вносить изменения автоматически.</p> 
			<p>Информация загружаемая из файлов yml не влият на ранее или позднее внесенные позиции вручную из личного кабинета, или введеннные операторами ввода информационного центра "Товары плюс".</p>	
		</div>
		<? if ($error_message) {?>
			<div class="error-info"><br/><?= $error_message?></div>
		<? }?>
		<div class="popup wide">
			<form action="/firm-user/price/upload/submit/" enctype="multipart/form-data" style="padding-top: 10px;" method="post">
				<div class="inputs">
					<label style="max-width: 90%;">Укажите ссылку для загрузки файла с данными (мы будем проверять обновления этого файла автоматически)
						<input type="text" name="url" placeholder="http://example.com/catalog.yml" />
					</label>
					<label style="max-width: 90%;">или загрузите файл с компьютера (допустимый формат файла: xml, yml)
						<input type="file" name="file" accept="yml" />
					</label>
					<label style="max-width: 90%;">Укажите какие поля задействовать из yml при формировании названия товара:</label>
					<div class="e-check-boxes">
						<ul>
							<label><li><input type="checkbox" class="e-check-box" name="name_format[]" value="typePrefix" />typePrefix</li></label>
							<label><li><input type="checkbox" class="e-check-box" name="name_format[]" checked="checked" value="name" disabled="disabled" />name</li></label>
							<label><li><input type="checkbox" class="e-check-box" name="name_format[]" value="vendor"  />vendor</li></label>
							<label><li><input type="checkbox" class="e-check-box" name="name_format[]" value="model"  />model</li></label>
						</ul>
					</div>
					<label style="max-width: 90%;">Укажите необходимо ли задействовать прямой переход на ваш сайт по ссылке конкретного товара:</label>
					<div class="e-check-boxes">
						<ul>
							<label><li><input type="checkbox" class="e-check-box" name="flag_is_referral" checked="checked" value="1" />Переход по реферальной ссылке</li></label>
						</ul>
					</div>
				</div>
				<button class="e-button send js-send" type="button" >Загрузить</button>
			</form>
		</div>
	</div>
	<div class="search_result">
		<div class="delimiter-block"></div>
		<?= app()->chunk()->render('firmuser.call_support_block')?>
	</div>
</div>