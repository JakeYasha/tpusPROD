<div class="popup wide">
    <form<?= html()->renderAttrs($attrs) ?>>
        <div class="top_field">
            <div class="title"><?= $heading ?></div>
        </div>
        <div class="red-block">Размещение информации на сайте идет на коммерческой основе от 1500 рублей</div>
        <div class="inputs">
            <? foreach ($fields as $field) { ?>
                <label><?= $field['label'] ?><?= $field['html'] ?></label>
            <? } ?>
            <br/>
            <div class="load_field">
                <span>Приложить файл к заявке</span>
                <button type="button" onclick="$(this).parent().find('input').click();">Загрузить файл</button>
                <input type="file" name="file1" onchange="$(this).parent().find('span').css('color', '#000').html(this.value);"/>
            </div>
            <!--div class="load_field">
                <span>Отправить свой баннер или новость</span>
                <button type="button" onclick="$(this).parent().find('input').click();">Загрузить файл</button>
                <input type="file" name="file2" onchange="$(this).parent().find('span').css('color','#000').html(this.value);"/>
            </div>
            <div class="load_field">
                <span>Отправить описание для фирмы</span>
                <button type="button" onclick="$(this).parent().find('input').click();">Загрузить файл</button>
                <input type="file" name="file3" onchange="$(this).parent().find('span').css('color','#000').html(this.value);"/>
            </div-->
            <br/><br/>
            <?= app()->chunk()->render('common.agreement_block') ?>
            <?= app()->capcha()->render() ?>
            <div class="error-submit" style="height: 40px;"></div>
            <?= $controls['submit']['html'] ?>
        </div>
    </form>
</div>