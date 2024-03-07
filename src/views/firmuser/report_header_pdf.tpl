<table>
	<tr>
		<th class="dark" style="text-align: left; border: none;vertical-align: middle;">
                        <?if (app()->firmUser()->val('id_service') == 10) {?><a href="<?=APP_URL?>"><img src="/img/727373-logo.png" style="height: 40px;" /></a><?}?>
			<a href="<?=APP_URL?>"><img src="/img/logo.png" style="height: 40px;" /></a>
		</th>
	</tr>
</table>
<div class="delimiter-block"></div>
<table class="table-header">
	<tr>
		<td style="width: 60%; padding-right: 20px;" rowspan="2">
			<p class="firm-name"><?=$firm->name()?></p><p><?=$firm->address()?></p>
		</td>
	</tr>
	<tr>
		<td style="text-align: left; width: 30%">
			<p><strong>Период отчета: </strong> <?=$period?></p>
			<p><strong>Дата отчета: </strong> <?=date('d.m.Y H:i:s')?></p>
		</td>
	</tr>
</table>