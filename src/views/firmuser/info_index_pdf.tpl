<div class="delimiter-block"></div>
<h2>Карточка фирмы</h2>
<h1 class="start"><?=$firm->name()?></h1>
<table>
	<tr>
		<th class="first" style="width: 30%">Параметр</th>
		<th style="width: 60%">Значение</th>
	</tr>
	<?  foreach ($data as $k=>$v) {?>
	<tr>
		<td class="first"><?=$k?></td>
		<td><?=$v?></td>
	</tr>
	<?}?>
</table>
<?if (isset($description) && $description) {?>
    <br/>
    <b>Дополнительная информация:</b>
    <div style="border:1px solid #ccc;padding:5px;"><?=$description?></div>
<?}?>
<div class="delimiter-block"></div>	
<p style="text-align: left;">Достоверность информации подтверждаю:</p>
<p style="text-align: right;">"____" _______________ 20___</p>
<div class="delimiter-block"></div>	
<p><strong>Специалист:</strong> <?=$manager->name()?></p>