<form<?= html()->renderAttrs($attrs) ?>>
    <div class="red-block">Размещение информации на сайте идет на коммерческой основе от 1500 рублей</div>
    <? foreach ($fields as $field) { ?>
        <div class="modal__item"><?= $field['label'] ?><?= $field['html'] ?></div>
    <? } ?>
    <br/>
    <div class="modal__item">
        <span>Приложить файл к заявке</span>
        <button type="button" onclick="$(this).parent().find('input').click();">Загрузить файл</button>
        <input type="file" name="file1" onchange="$(this).parent().find('span').css('color', '#000').html(this.value);"/>
    </div>
    <div class="modal__item">
    <?= app()->chunk()->render('common.agreement_block') ?>
    </div>
    <?= app()->capcha()->render() ?>
    <div class="error-submit" style="height: 40px;"></div>
    <?= $controls['submit']['html'] ?>
</form>