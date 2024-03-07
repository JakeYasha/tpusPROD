<?if($items){?>
<?$filters = app()->request()->processGetParams([
			'page' => 'int',
			'query' => 'string',
			'sorting' => 'string'
		]);?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th>Фирма</th>
			<th style="width: 80px;">Рейтинг</th>
			<th style="width: 60px;">Дата обновления</th>
			<th style="width: 30px;">&nbsp;</th>
		</tr>
		<?$i=0;foreach ($items as $item) {$i++;?>
		<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?><?if(CDateTime::toTimestamp($item['timestamp_inserting']) > CDateTime::toTimestamp(app()->firmManager()->val('prev_logon_timestamp'))){?> class="new"<?}?>>
			<td>
                <?if ($item['id_firm_user'] < 1) {?>
                    <span class="ui-icon ui-icon-notice information tooltip" title="Для этой фирмы личный кабинет ещё не был создан" style="display: inline-block;margin-top: -2px;float:left;"></span>
                <?}?>
                <?if ($item['is_lead']) {?>
                    <span class="ui-icon ui-icon-person information tooltip" title="Кабинет пользователя <?=$item['email']?> привязан к этой фирме" style="display: inline-block;margin-top: -2px;float:left;"></span>
                <?}?>
                <div class="clearfix">
                    <a class="left_block" target="_blank" href="/firm-manager/set-firm/<?=$item['id']?>/" style="max-width: calc(100% - 32px);"><?=$item['name']?></a>
                    <br/>
                    <div class="firm-rating">
                        <?= app()->chunk()->setArgs([$item['rating'], true])->render('rating.stars')?>
                    </div>
                </div>
                <p class="description"><?=$item['address']?></p>
            </td>
			<td><?= app()->chunk()->setArgs([$item['rating'], true])->render('rating.stars')?></td>
			<td><p style="text-align: right;"><?=date('d.m.Y', CDateTime::toTimestamp($item['timestamp_last_updating']))?></p><p class="description" style="text-align: right;"><?=date('H:i:s', CDateTime::toTimestamp($item['timestamp_inserting']))?></p></td>
			<td style="vertical-align: middle; text-align: center;">
				<?if ($item['id_firm_user'] > 0) {?>
					<a title="" href="/firm-manager/firm-user/?mode=edit&id_firm=<?=$item['id']?>&page=<?=$filters['page']?>&sorting=<?=$filters['sorting']?>&query=<?=urlencode($filters['query'])?>" class="edit-btn fancybox fancybox.ajax"></a>
				<?} else {?>
					<a title="" href="/firm-manager/firm-user/?mode=add&id_firm=<?=$item['id']?>&page=<?=$filters['page']?>&sorting=<?=$filters['sorting']?>&query=<?=urlencode($filters['query'])?>" class="add-btn fancybox fancybox.ajax"></a>
				<?}?>
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?}?>