<div class="search_result" style="border-top: none;">
    <div class="attention-info">
        <p>Внимание! Важная информация...</p>
    </div>
</div>
<div class="popup wide">
    <div class="top_field">
        <div class="title">Загрузка изображения рекламного модуля и его миниатюры</div>
    </div>
    <div class="inputs">
        <div class="col" style="display: inline-block;">
            <div class="firm_field">
                <div class="image_field">
                    <div class="image"><span><img class="js-upload-attach-holder advert-module-full-image" src="<?= $full_image_url ?>" /></span></div>
                </div>
            </div>
            <?= app()->chunk()->set('url', '/firm-user/ajax/upload/advert-module-full-image/')->set('label', 'Загрузить модуль')->render('elem.file_upload_button') ?>
        </div>
        <div class="col" style="display: /*inline-block*/ none;">
            <div class="firm_field">
                <div class="image_field">
                    <div class="image"><span><img class="js-upload-attach-holder advert-module-image" src="<?= $image_url ?>" /></span></div>
                </div>
            </div>
            <?/*= app()->chunk()->set('url', '/firm-user/ajax/upload/advert-module-image/')->set('label', 'Загрузить миниатюру')->render('elem.file_upload_button') */?>
        </div>
        <div class="attention-info">
            <!--p>Изображение рекламного модуля обязательно и будет показано на странице со списком активных рекламных модулей. Миниатюра будет использоваться для отображения рекламного модуля в рекламных блоках, которые могут размещаться на страницах карточек фирм.</p-->
            <p>
                Загрузите миниатюру и оригинал для рекламного модуля в любом из форматов: JPG, GIF, PNG. </br>
                <strong>Рекомендуемый</strong> размер оригинала модуля не более 500 на 500 пикселей.</br>
                <!--strong>PS! Размещение миниатюры не обязательно.</strong> Рекомендуемый размер миниатюры 310 на 150 пикселей.</br-->
                Размер файла не должен превышать 2 Мб.
            </p>
        </div>
    </div>
</div>
<form<?= html()->renderAttrs($attrs) ?>>
    <div class="popup wide">
        <div class="top_field">
            <div class="title"><?= $mode === 'edit' ? 'Изменение материалов по рекламному модулю' : 'Добавление материалов по рекламному модулю' ?></div>
        </div>
        <div class="inputs">
            <? foreach ($fields as $name => $field) { ?>
                <? if (in_array($name, ['phone', 'email', 'subgroup_ids', 'firm_type_ids', 'more_url', 'target_btn_name', 'callback_btn_name'])) {
                    continue;
                } ?>
                <? if ($field['elem'] !== 'hidden_field') { ?>
                    <label style="max-width: 90%;"><?= $field['label'] ?><?= $field['html'] ?></label>
                <? } else { ?>
                    <?= $field['html'] ?>
				<? } ?>
			<? } ?>
        </div>
        <div class="error-submit"></div>

    </div>
    <div class="popup wide" style="display: none;">
        <div class="top_field">
            <div class="title">Выбор шаблона модуля</div>
        </div>
        <div class="inputs">
            <div class="col" style="display: inline-block;">
                <div class="firm_field">
                    <select name="type" class="def sdvert_module_type_selector">
                        <? foreach ($types as $key => $value) { ?>
                            <option value="<?= $key !== '' ? $key : 'default' ?>" <? if ($key == $type) { ?>selected="selected"<? } ?>><?= $value ?></option>
<? } ?>
                    </select>
                </div>
            </div>
            <div class="search_adv_module_block advert_module_block_wrapper" <? if ($type != 'default_advert_module') { ?>style="display: none;"<? } ?>>
                <div class="search_adv_module_block_wrapper">
<?= app()->chunk()->setArg($item)->render('adv.advert_module_default') ?>
                </div>
            </div>
            <div class="search_adv_module_block_wide advert_module_block_wrapper" <? if ($type != 'wide_advert_module') { ?>style="display: none;"<? } ?>>
                <div class="search_adv_module_block_wide_wrapper">
<?= app()->chunk()->setArg($item)->render('adv.advert_module_wide') ?>
                </div>
            </div>

            <div class="attention-info">
                <p>
                    Выберите шаблон для отображения рекламного модуля
                </p>
            </div>
        </div>
    </div>
    <div class="popup wide">
            <div class="top_field">
                <div class="title">Тематика рекламного модуля</div>
            </div>
            <div class="inputs">
                <div class="attention-info">
                    <p>Для возможности фильтрации рекламных модулей по тематикам, закрепите за модулем несколько подходящих по тематике рекламы подгрупп (на основе подгрупп РАТИСС). Для этого кликните по пустому месту поля ввода рубрик и начните вводить название подгруппы. В появившемся списке выберите необходимую подгруппу и кликните по ней. Для добавления еще одной подгруппы, повторите действия.</p>
                </div>
                <label style="max-width: 90%;"><?= $fields['subgroup_ids']['label'] ?><?= $fields['subgroup_ids']['html'] ?></label>
				<div class="error-submit"></div>
			</div>
	</div>		

    <div class="popup wide">
        <div class="top_field">
            <div class="title">Настройка кнопки формы обратной связи</div>
        </div>
        <div class="inputs">
            <div class="attention-info">
                <p>Для отображения у рекламного модуля кнопки вызова формы обратной связи, для оформления заявки на запись или покупку рекламируемого товара или услуги, заполните сотовый телефон и(или) email рекламодателя.</p>
                <p>Если будет введен сотовый номер телефона, при заполнении формы, рекламодателю будет отправлена смс с сообщением следующего содержания - "Посетитель сайта TovaryPlus.ru ХХХ (номер телефона ХХХ) интересуется рекламным модулем ХХХ". Формат ввода номера +79996634351</p>
                <p>Если будет введен email, то при заполнении формы, рекламодателю будет отправлено письмо с сообщением о новом заказе по рекламному модулю.</p>
            </div>
            <label style="max-width: 90%;"><?= $fields['email']['label'] ?><?= $fields['email']['html'] ?></label>
            <label style="max-width: 90%;"><?= $fields['phone']['label'] ?><?= $fields['phone']['html'] ?></label>
            <label>Текст кнопки</label>
            <? foreach ($callback_btn_names as $key => $value) { ?>
                    <label><?=$value?><input <?if($callback_btn_name == $key){?> checked="checked"<?}?> type="radio" name="callback_btn_name" class="e-check-box grey" value="<?=$key?>"/></label>
            <?}?>
        </div>
        <div class="error-submit"></div>
    </div>
    <div class="popup wide">
        <div class="top_field">
            <div class="title">Настройка кнопки ссылки на целевую страницу модуля</div>
        </div>
        <div class="inputs">
            <label style="max-width: 90%;"><?= $fields['more_url']['label'] ?><?= $fields['more_url']['html'] ?></label>
            <label>Текст кнопки</label>
            <? foreach ($target_btn_names as $key => $value) { ?>
                    <label><?=$value?><input <?if($target_btn_name == $key){?> checked="checked"<?}?> type="radio" name="target_btn_name" class="e-check-box grey" value="<?=$key?>"/></label>
            <?}?>
            <div class="attention-info">
                <p>Скопируйте и вставьте ссылку на тематическую страницу по рекламному модулю для перенаправления посетителя сайта. Это может быть страница на сайте рекламодателя или ссылка на страницу сайта Tovaryplus.ru</p>
            </div>

        </div>
        <div class="error-submit"></div>
        <?= $controls['submit']['html'] ?>
    </div>

    <div class="popup wide" style="display: none;">
            <div class="top_field">
                <div class="title">Настройка вывода рекламного блока в карточках фирм</div>
            </div>
            <div class="inputs">
                <div class="attention-info">
                    <p>Если Вы хотите дополнительно размещать рекламный блок типа текстового баннера, на основе рекламного модуля в карточках фирм, задайте несколько подходящих по тематике модуля рубрик из каталога фирм. При отображении карточки фирмы относящейся к вашему региону и если фирма отнесена к выбранной рубрике, будет показан рекламный блок.</p>
                </div>
               <label style="max-width: 90%;"><?= $fields['firm_type_ids']['label'] ?><?= $fields['firm_type_ids']['html'] ?></label>
            </div>
            <div class="error-submit"></div>
    </div>


</form>
