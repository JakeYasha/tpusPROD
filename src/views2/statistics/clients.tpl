<?= $breadcrumbs?>
<?=app()->chunk()->render('common.print_button')?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<?= $text->val('text')?>
		<? foreach ($items as $year => $firms) {?>
			<h2><?= $year?></h2>
			<? foreach ($firms as $firm) {?>
				<a href="<?=$firm->link()?>"><?= $firm->name()?></a>
				<p><?=$firm->activity()?></p>
			<? }?>
		<? }?>
	</div>
</div>