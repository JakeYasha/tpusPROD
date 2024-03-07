<?= $breadcrumbs?>
<?=app()->chunk()->render('common.print_button')?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<?= app()->metadata()->replaceLocationTemplates($text->val('text'))?>
		<table class="cltable" style="width: 100%;">
			<tr>
				<th>Раздел</th>
				<th>Количество фирм</th>
			</tr>
			<?foreach($subgroups as $sgr) {if(!isset($matrix[$sgr['id_subgroup']]))continue;?>
				<tr>
					<th colspan="2"><strong><?=$sgr['name']?></strong></th>
				</tr>
				<?  foreach ($matrix[$sgr['id_subgroup']] as $item) {?>
					<tr>
						<td><a href="/<?=app()->location()->currentId()?><?=$item['link']?>"><?=$item['name']?></a></td>
						<td><?=$item['count']?></td>
					</tr>
				<?}?>
			<?}?>
		</table>
	</div>
</div>