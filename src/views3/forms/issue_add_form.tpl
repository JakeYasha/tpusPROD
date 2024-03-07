<div class="popup wide">
    <div class="top_field">
        <div class="title">Загрузка изображения выпуска</div>
    </div>
    <div class="inputs">
        <div class="col" style="display: inline-block;">
            <div class="firm_field">
                <div class="image_field">
                    <div class="image">
                        <span>
                            <img class="js-upload-attach-holder issue-full-image" src="<?= $full_image_url ?>" />
                        </span>
                    </div>
                </div>
            </div>
            <?= app()->chunk()->set('url', '/firm-manager/ajax/upload/issue-image/')->set('label', 'Загрузить изображение')->render('elem.file_upload_button') ?>
        </div>
        <div class="col" style="display: /*inline-block*/ none;">
            <div class="firm_field">
                <div class="image_field">
                    <div class="image">
                        <span>
                            <img class="js-upload-attach-holder issue-image" src="<?= $image_url ?>" />
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="attention-info">
            <p>
                Загрузите миниатюру и оригинал изображения выпуска в любом из форматов: JPG, GIF, PNG. </br>
                <strong>Рекомендуемый</strong> размер оригинала модуля не более 500 на 500 пикселей.</br>
                Размер файла не должен превышать 2 Мб.
            </p>
        </div>
    </div>
</div>
<div class="popup wide">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?=$mode === 'edit' ? 'Изменить данные выпуска' : 'Добавление нового выпуска'?></div>
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
                
<?
if (isset($model_id)){
?>           

<div class="black-block">Используемые материалы</div>
    <div class="search_result in-firm-manager" style="border-top: none;">
        <div class="delimiter-block"></div>
        <div class="search_price_field">
            <form action="/firm-manager/materials/" method="get">
                <input placeholder="Поиск по материалам..." class="e-text-field" type="text" name="query"<? if (isset($filters['query']) && $filters['query']) { ?> value="<?= $filters['query'] ?>"<? } ?> />
                <input type="submit" value="" class="submit">
            </form><?= $sorting ?>
        </div><br/>
        <!--<a href="/firm-manager/material/" class="default-red-btn" style="margin-left: 20px;">+ Новый материал</a>-->
        <div class="delimiter-block"></div>
        <?= $have_items ?>
    </div>



    <div class="black-block">Материалы</div>
    <div class="search_result in-firm-manager" style="border-top: none;">
        <div class="delimiter-block"></div>
        <div class="search_price_field">
            <form action="/firm-manager/materials/" method="get">
                <input placeholder="Поиск по материалам..." class="e-text-field" type="text" name="query"<? if (isset($filters['query']) && $filters['query']) { ?> value="<?= $filters['query'] ?>"<? } ?> />
                <input type="submit" value="" class="submit">
            </form><?= $sorting ?>
        </div><br/>
        <!--<a href="/firm-manager/material/" class="default-red-btn" style="margin-left: 20px;">+ Новый материал</a>-->
        <div class="delimiter-block"></div>
        <div class="cat_description">
            <p>Найдено: <?= $items_count ?></p>
        </div>
        <?= $items ?>
        <?= $pagination ?>
    </div>
<?
}
?>
