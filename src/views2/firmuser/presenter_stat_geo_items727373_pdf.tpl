<?if($items['data']){?>
	<div class="delimiter-block"></div>
	<h2>География посетителей *</h2>
	<table>
		<tr>
			<th>Город</th>
			<th class="last">Посетители</th>
		</tr>
		<?foreach ($items['data'] as $item) {?>
		<tr>
			<td><?=$item['name']?></td>
			<td class="last"><?=$item['count']?></td>
		</tr>
		<?}?>
	</table>
	<div class="delimiter-block"></div>
	<div class="attention-info" style="font-size: 0.85em;">
		<div>* Отчет "География посетителей" показывает, сколько посетителей и из каких регионов просматривали страницы сайта 727373.ru, где упоминалась информация о Вашей фирме. В отчет выводится 10 регионов с максимальным количеством посетителей. Определение региона идет на основе информации об ip адресе посетителя (провайдер, город и страна регистрации).</div>
	</div>
<?}?>