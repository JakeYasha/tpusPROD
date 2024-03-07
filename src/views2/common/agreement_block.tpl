<?
$text = new App\Model\Text();
$text->getByLink('agreement_block');
html()->jsFile('/js/agreement.js');
?>

<script src="/js/agreement.js"></script>
<div class="agreement-block">
    <?= $text->val('text') ?>
</div>