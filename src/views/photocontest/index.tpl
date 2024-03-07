<?= $bread_crumbs?>
<div class="cat_description">
	<h1>Фотоконкурсы</h1>
	<?= str()->replace($text->val('text'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()])?>
</div>
<div class="item_info">
	<div class="search_result promo">
	<? if ($items) {?>
		<? $i = 0;
		foreach ($items as $item) {
			$i++;?>
			<div class="search_result_cell border-bottom-block photo-contest-list">
				<div class="image" style="position: relative; width: 100%; border: none;"><a href="<?= $item['link']?>"><? if ($item['image_url']) {?><img style="width: 100%;" src="<?= $item['image_url']?>" alt="<?=  encode($item['name'])?>" /><? }?></a></div>
				<div class="description description-promo">
					<div class="title"><a href="<?= $item['link']?>"><?= $item['name']?></a></div>
					<?= $item['brief_text']?>
					<div class="org">
						<p>Дата проведения: <span class="infinite">с <?=$item['date_start']?> по <?=$item['date_end']?></span></p>
					</div>
					<div class="button_set">
						<?if($item['working'] && !$item['finished']){?>
							<a class="photo-contest-prize" href="<?=$item['link']?>">Участвовать</a>
						<?} elseif($item['finished']) {?>
							<a class="photo-contest-results" href="<?=$item['link']?>">Результаты</a>
						<?}?>
					</div>
					<?if($item['sponsor_name']){?>
					<div class="contacts">
						<div class="title">призы от партнера</div>
						<p><a target="_blank" rel="nofollow" href="<?=app()->away($item['sponsor_url'])?>"><?=$item['sponsor_name']?></a></p>
					</div>
					<?}?>
					<?/*
					<div class="org">
						<p>Срок действия: <span class="infinite"><?= $item['flag_is_infinite'] ? 'Постоянная акция' : 'с ' . $item['time_beginning'] . ' по ' . $item['time_ending']?></span></p>
						<?$firm = $item['firm']?>
						<p>Телефон для справок: <?=$item['phone']?><br /><a href="<?= $firm->linkItem()?>"><?= $firm->name()?></a></p>
					</div>
					 */?>
				</div>	
			</div>
		<? }?>
	<?}?>
	</div>
</div>