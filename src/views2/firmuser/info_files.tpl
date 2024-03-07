<div class="popup wide">
	<div class="top_field">
		<div class="title">Файлы для скачивания</div>
	</div>
	<div class="inputs">
		<div class="attention-info">
			<p>При необходимости Вы можете разместить на страницах с информацией о Вашей компании ссылки для скачивания файлов с прейскурантом или оригинальным прайс-листом, отсканированные документы, лицензии, сертификаты.  Информация о файлах будет опубликована в соответсвующем блоке, а файлы доступны для скачивания. Форматы загружаемых файлов: doc(x), xls(x), pdf</p>
			<p>Название файла при выводе в блоке на сайте, будет являться ссылкой. Для того, чтобы посетители понимали какие файлы они могут посмотреть, рекомендуем перед загрузкой на сайт переименовать (называть) файлы понятными именами. Например: "Прайс на метизы", "Прейскурант на 2015 год"</p>
			<p>Рекомендуемый размер загружаемых файлов до 10 Mb</p>
		</div>
		<div class="js-upload-attach-holder-images uploaded-files">
			<ul>
				<?foreach($files as $img){?><li>
					<div class="img"><a href="<?=$img->link()?>" rel="nofollow"><img src="<?=$img->thumb()?>" /></a></div>
					<div class="name">
						<a href="<?=$img->link()?>"  rel="nofollow"><?=$img->name()?></a>
						<span><?=$img->val('file_extension')?>, <?=$img->getFormatSize("", 0)?></span>
					</div>
					<a href="#" class="js-action js-remove-firm-file img-del-btn" data-id="<?=$img->id()?>" rel="nofollow"></a>
				</li><?}?>
			</ul>
		</div>
		<?=app()->chunk()->set('url', '/firm-user/ajax/upload/firm-files/?id_firm='.$id_firm.'&id_service='.$id_service)->set('accept','.doc,.docx,.pdf,.xls,.xlsx')->render('elem.files_upload_button')?>
	</div>
</div>
<div class="delimiter-block"></div>