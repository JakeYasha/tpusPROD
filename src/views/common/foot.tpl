<div class="js-is-mobile"></div>
<? /* <a href="#" class="online_consult js-open-operator-dialog-btn"><img src="/css/img/oc.jpg" alt=""/></a> */ ?>
<?
if (APP_IS_DEV_MODE) {
    $files = App\Controller\Common::getJsFiles();
    foreach ($files as $file) {
        echo html()->jsFile($file.'?v'.time());
    }
} else {
    ?>
    <?= html()->jsFile('/js/js.min.js?v' . RESOURCE_UPDATE_TIME) ?>
<? } ?>
<?= app()->metadata()->renderJsFiles() ?>
<?= app()->metadata()->renderJs() ?>
<? if (app()->useMap()) { ?>
    <?= html()->jsFile('https://api-maps.yandex.ru/2.1/?lang=ru-RU') ?>
    <?= html()->jsFile('/js/ymaps.js') ?>
<? }
if (app()->useAgreement()) {
    ?>
    <?= html()->jsFile("/js/agreement.js"); ?>
    <?= html()->js("$(function () { grecaptcha.render('recapcha', {'sitekey': '6LcIGQcTAAAAAACH0lEII2K4AOdepy7ngyGzteEr'});});"); ?>
<?
}?>