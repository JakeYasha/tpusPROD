<?
$text = new App\Model\Text();
$text->getByLink('personal_data');
?>
<div class="personal-data-block" data-modal="true">
    <div class="personal-data-block__wrapper"><?= $text->val('text') ?></div>
    <div class="personal-data-block__close js-close-personal"></div>
</div>