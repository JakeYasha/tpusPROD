<div class="popup wide">
    <form<?= html()->renderAttrs($attrs) ?>>
        <div class="top_field">
            <div class="title"><?= $heading ?></div>
        </div>
        <p class="text"><?= $sub_heading ?></p>
        <div class="inputs">
            <? foreach ($fields as $field) { ?>
                <label><?= $field['label'] ?><?= $field['html'] ?></label>
            <? } ?>
            <?= app()->chunk()->render('common.agreement_block') ?>
        </div>
        <div class="g-recaptcha" data-sitekey="6LcIGQcTAAAAAACH0lEII2K4AOdepy7ngyGzteEr"></div>
        <div class="error-submit"></div>
        <?= $controls['submit']['html'] ?>
    </form>
</div>