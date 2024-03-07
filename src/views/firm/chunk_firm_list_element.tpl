<div class="firm_field">
	<div class="image_field image_field1">
		<div class="image"><a href="<?=$item->link()?>"><img<?if(!$item->hasLogo()){?> class="no-image"<?}?> src="<?= $item->logoPath()?>" alt="<?=str()->replace($item->name(), ['"'], ['&quot;'])?>, <?=str()->replace($item->address(), ['"'], ['&quot;'])?>"></a></div>
	</div>
	<div class="description">
		<a href="<?=$item->link()?>" class="h1<? if ($item->isBlocked()) {?> gray<?}?>"><?= $item->name()?></a>
		<?if(!isset($show_rating) || $show_rating){?><?= app()->chunk()->setArg($item)->render('rating.stars')?><?}?>
		<? if ($item->hasActivity()) {?><p><?= $item->activity()?></p><? }?>
		<div class="contacts">
			<div class="title">контакты:</div>
			<? if ($item->hasPhone()) {?><p><span class="l">Телефон:</span><span class="r tel"><?=app()->chunk()->set('item', $item)->render('firm.chunk_phone')?></span></p><? }?>
			<p><span class="l">Адрес:</span><span class="r"><?= $item->address()?></span></p>
			<? if ($item->hasModeWork()) {?><p><span class="l">Режим работы:</span><span class="r"><?= $item->modeWork()?></span></p><? }?>
			<? if ($item->hasWebPartner()) {?><p><span class="l">Сайт:</span><span class="r"><a target="_blank" href="/page/away/firm/<?=$item->id().'/'?>" rel="nofollow"><?= $item->webSiteMain()?></a></span></p><? } else {?>
			<? if ($item->hasWeb()) {?><p><span class="l">Сайт:</span><span class="r"><a target="_blank" href="<?=app()->away($item->webSiteMain(), $item->id())?>" rel="nofollow"><?= $item->webSiteMain()?></a></span></p><? }?>
			<?}?>
		</div>
		<?if(isset($catalogs_count[$item->id()])){?>
		<div class="prices<?if(count($catalogs_count[$item->id()]) > 3) {?> with-hider closed<?}?>">
			<div class="title">товары и услуги:</div>
			<ul class="catalog-links">
                <li>
                <?$i=0;$_max_childs=12;$count=count($catalogs_count[$item->id()]);foreach($catalogs_count[$item->id()] as $cat_id => $count) {if(!isset($catalogs[$cat_id]))continue;$i++;if ($i>$_max_childs)break;?>
                    <?if ($i == 1) {?>
                            <a rel="nofollow" href="<?=$catalogs[$cat_id]->linkPriceList($item)?>"><?=$catalogs[$cat_id]->name()?></a>&nbsp;<span><?=$catalogs_count[$item->id()][$cat_id]?></span><?=$count>1 ? ': ': ''?>
                    <?} else {?>
                            <span><?=$catalogs[$cat_id]->val('web_name')?>; <?=$i==$_max_childs && $count>$_max_childs ? '...' : ''?></span>
                    <?}?>
                <?}?>
                </li>
			</ul>
			<div class="show_more"><div class="line"></div><a rel="nofollow" href="<?=$item->linkPricelist((isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null)?>"><span>Показать все предложения</span></a></div>
		</div>
		<?}?>
		<?if(isset($special_price_links[$item->id()])){?>
		<div class="prices<?if(count($special_price_links[$item->id()]) > 3) {?> with-hider closed<?}?>">
			<div class="title">товары и услуги:</div>
			<ul class="catalog-links">
			<?$i=0;foreach($special_price_links[$item->id()] as $link) {$i++;?>
				<li<?if($i>3){?> class="hidden"<?}?>><a href="<?=$link['url']?>" rel="nofollow"><?=$link['name']?></a></li>
			<?}?>
			</ul>
			<?if(count($special_price_links[$item->id()]) > 3) {?>
			<div class="show_more"><div class="line"></div><a href="#" class="js-show-more-catalogs" data-model-alias="firm" data-model-id="<?=$item->id()?>"><span>Показать все</span></a></div>
			<?}?>
		</div>
		<?}?>
	</div>
	<?if(!isset($show_buttons) || $show_buttons){?>
	<div class="button_set">
		<?if($item->hasPriceList()){?><a class="price_list" href="<?= $item->linkPricelist()?>" rel="nofollow">Прайс-лист</a><?}?>
		<?if($item->hasEmail()){?><a class="write fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/<?=$item->id()?>/" rel="nofollow">Отправить сообщение</a><?}?>
		<?if($item->hasCellPhone() && $item->id_service() == 10){?><a class="cell_phone fancybox fancybox.ajax" href="/firm-feedback/get-callback-form/<?=$item->id()?>/" rel="nofollow">Перезвоните мне</a><?}?>
	</div>
	<?}?>
</div>