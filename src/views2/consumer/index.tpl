<?= $bread_crumbs?>
<div class="for_clients clearfix guest-book">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<h1><?= $item->name()?></h1>
                <?if($errors) {?>
			<ul>
			<?  foreach ($errors as $k=>$v) {?>
				<li><?=$v['message']?></li>
			<?}?>
			</ul>
		<div class="all_block">
			<?=$form?>
			</div>
		<?} else {?>
                    <?if(!$short_view){?>
		<?=$item->val('text')?>
		<div class="all_block">
			<?=$form?>
		</div>
		<br/>
                    <?}?>
                <?}?>
		<h2 class="black_border_bottom">Вопросы и ответы</h2>
		<br/>
		<?=$items?>
	</div>
	<div class="search_result">
		<?=$pagination?>
	</div>
</div>