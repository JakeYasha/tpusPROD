<?if($items){?>
<table class="default-table banner-table" style="width: 100%;">
	<tr>
		<th style="width: 35px;">#</th>
		<th style="width: 280px;">Рекламные модули</th>
		<th style="width: 30%;">Заголовок и описание</th>
                <th style="width: 30%;">Рубрики каталога товаров и услуг / Рубрики каталога фирм</th>
		<th style="max-width: 100px;">Период размещения</th>
		<th style="max-width: 100px;">Показы</th>
		<th style="max-width: 100px;">Клики</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?>>
		<td><?=$item['id']?></td>
		<td style="max-width: 300px; text-align: center;">
                        <a href="/firm-manager/set-firm/<?=$item['firm']->id()?>/" target="_blank"><?=$item['firm']->name()?></a>
                        <br/>
                        <br/>
                        <img width="200px" src="<?=$item['full_image']?>"/>
                </td>
                <td style="max-width: 200px;">
                        <strong><?=$item['header']?></strong>
                        <hr/>
                        <p><?=$item['adv_text']?></p>
                        <hr/>
                        <p>
                            <dl>
                                <?=$item['advert_module_url'] ? '<dt>ссылка на главную страницу:</dt><dd>'.$item['advert_module_url'].'</dd><br/>' : ''?>
                                <?=$item['advert_module_more_url'] ? '<dt>ссылка на целевую страницу:</dt><dd><a href="' . app()->away($item['advert_module_more_url']) . '" target="_blank">'.(strlen($item['advert_module_more_url']) > 50 ? substr($item['advert_module_more_url'],1,50) . '...' : $item['advert_module_more_url']).'</a></dd><br/>' : ''?>
                                <?=$item['email'] ? '<dt>email для заказа:</dt><dd>'.$item['email'].'</dd><br/>' : ''?>
                                <?=$item['phone'] ? '<dt>телефон для SMS заказа:</dt><dd>'.$item['phone'].'</dd><br/>' : ''?>
                            </dl>
                        </p>

                </td>
		<td class="banner-table-subgroup">
                        <dl>
                                <?if(isset($item['subgroups']) && count($item['subgroups']) > 0){?>
                                        <dt>Рубрики каталога товаров и услуг</dt>
                                        <?foreach($item['subgroups'] as $cat){?>
                                                <dd><a target="_blank" href="<?=app()->link($cat->link())?>"><?=$cat->name()?></a></dd>
                                        <?}
                                }?>
                                <br/>
                                <?if(isset($item['firmtypes']) && count($item['firmtypes']) > 0){?>
                                        <dt>Рубрики каталога фирм</dt>
                                        <?foreach($item['firmtypes'] as $firmtype){?>
                                                <dd><a target="_blank" href="<?=app()->link($firmtype->link())?>"><?=$firmtype->name()?></a></dd>
                                        <?}
                                }?>
                </td>
		<td><?=$item['period']?></td>
		<td><?=$item['total_views']?></td>
		<td><?=$item['total_clicks']?></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
