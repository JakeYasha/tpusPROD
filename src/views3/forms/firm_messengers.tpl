<div class="popup wide">
    <div class="top_field">
        <div class="title">Мессенджеры/Социальные сети</div>
    </div>
    <div class="inputs">
        <div class="attention-info">
            <p>Здесь вы можете изменить информацию о фирме размещенную на сайте. После того как Вы нажмете кнопку "Сохранить" информация появится на сайте.</p>
        </div>	
    </div>	
    <form<?= html()->renderAttrs($attrs) ?>>
        <div class="inputs">
            <? foreach ($fields as $field) { ?>
                <? if ($field['elem'] !== 'hidden_field') { ?>
                    <label style="max-width: 90%;"><?= $field['label'] ?><?= $field['html'] ?></label>
                <? } else { ?>
                    <?= $field['html'] ?>
                <? } ?>
            <? } ?>
        </div>
        <div class="error-submit">
            <? if (isset($errors)) { ?>
                <? foreach ($errors as $error) { ?>
                    <p><?= $error['message'] ?></p>
                <? } ?>
            <? } ?>
        </div>

        <?= $controls['submit']['html'] ?>
    </form>
</div>