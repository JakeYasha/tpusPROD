<a href="#" onclick="window.open('/print/?print_url=<?= app()->request()->getRequestUri() ?>', 'print', 'menubar=yes,location=no,resizable=yes,scrollbars=yes,status=yes')" class="print-button">Версия для печати</a>
<?= $bread_crumbs ?>
<div class="for_clients clearfix">
    <div class="for_clients_text_c clearfix page">
        <? if ($item) {?>
            <?= $item->val('text') ? $item->val('text') : 'Текст материала еще не был сгенерирован' ?>
        <? } ?>
    </div>
</div>