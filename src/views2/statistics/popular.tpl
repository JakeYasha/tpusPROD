<?= $breadcrumbs?>
<?=app()->chunk()->render('common.print_button')?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<?= app()->metadata()->replaceLocationTemplates($text->val('text'))?>
		<?/*=  app()->chunk()->set('items', $chart_items)->set('title', '')->render('charts.donut')*/?>
		<table class="cltable" style="width: 100%;">
			<tr>
				<th>Страница</th>
				<th>Количество</th>
			</tr>
			<?  foreach ($items as $item) {?>
			<tr>
				<td><a href="<?=$item['response_url']?>"><?=$item['response_title']?></a></td>
				<td><?=$item['cnt']?></td>
			</tr>
			<?}?>
		</table>
	</div>
</div>