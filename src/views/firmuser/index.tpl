<?=$bread_crumbs?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page">
	<?=$text->name()?>
	<?=$text->val('text')?>
	<?=app()->chunk()->render('firmuser.call_support_block')?>
	</div>
</div>