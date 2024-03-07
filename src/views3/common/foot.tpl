<div class="js-is-mobile"></div>
<? /* <a href="#" class="online_consult js-open-operator-dialog-btn"><img src="/css/img/oc.jpg" alt=""/></a> */ ?>
<?
if (APP_IS_DEV_MODE) {
    $files = App\Controller\Common::getJs3Files();
    foreach ($files as $file) {
        echo html()->jsFile($file);
    }
} else {
    ?>
    <?= html()->jsFile('/js3/js.min.js?v' . RESOURCE_UPDATE_TIME) ?>
<? } ?>
<?= html()->jsFile('https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js') ?>
<?= html()->jsFile('/js3/main.min.js?v' . RESOURCE_UPDATE_TIME) ?>
<?= html()->jsFile('/js3/ext.min.js?v' . RESOURCE_UPDATE_TIME) ?>
<?= html()->jsFile('/js3/modal.js?v' . RESOURCE_UPDATE_TIME) ?>
<?= html()->jsFile('/js3/js3.min.js?v' . RESOURCE_UPDATE_TIME) ?>
<?= app()->metadata()->renderJsFiles() ?>
<?= html()->jsFile('/jsall/jsall.js') ?>
<?= app()->metadata()->renderJs() ?>
<? if (app()->useMap()) { ?>
    <?= html()->jsFile('https://api-maps.yandex.ru/2.1/?lang=ru-RU') ?>
    <?= html()->jsFile('/js3/ymaps.js') ?>
<? }
if (app()->useAgreement()) {
    ?>
    <?= html()->jsFile("/js3/agreement.js"); ?>
    <?= html()->jsFile("https://www.google.com/recaptcha/api.js"); ?>
    <?= html()->js("$(function () { grecaptcha.render('recapcha', {'sitekey': '6LcIGQcTAAAAAACH0lEII2K4AOdepy7ngyGzteEr'});});"); ?>
<?
}?>