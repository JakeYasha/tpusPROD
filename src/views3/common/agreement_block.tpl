<?
$text = new App\Model\Text();
$text->getByLink('agreement_block');
html()->jsFile('/js3/agreement.js');
?>

<div class="modal__item">
    <?= $text->val('text') ?>
</div>