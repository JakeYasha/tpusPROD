<?if(!$item->isBlocked()){?>
<div class="item_info">
	<div class="search_result">
		<?= $tabs?>
		<?if($item->hasAbout()){?>
		<div class="firm-bottom-block cat_description">
			<?=$item->about()?>
		</div>
		<?}?>
		<?if($item->hasDescription()){?>
		<div class="firm-bottom-block">
			<h2>Дополнительная информация</h2>
			<p><?=$item->description()?></p>
		</div>
		<?}?>
		<?if($item->hasGallery()){?>
		<div class="jcarousel-wrapper w320">
			<div class="jcarousel js-jcarousel">
				<ul>
					<?foreach ($gallery as $image){?>
					<li><a data-fancybox-group="images_gallery" href="<?=$image->path()?>" class="fancybox"><img alt="<?=encode($item->name())?>, <?=$item->getCity()?>" src="<?=$image->path('-330x200')?>" /></a></li>
					<?}?>
				</ul>
			</div>
			<a class="jcarousel-control-prev"></a>
			<a class="jcarousel-control-next"></a>
		</div>
		<?}?>
        <?=app()->chunk()->render('adv.firm_advert_modules')?>
		<?= $popular_items?>
		<?if($big_catalogs){?>
		<div class="cat_description" style="margin-left: 0;">
			<p>Некоторые группы товаров и услуг из прайс-листа фирмы:</p>
			<ul>
				<?foreach ($big_catalogs['matrix'] as $id_parent => $childs) {?>
                    <?if ($big_catalogs['data'][$id_parent]->val('node_level') < 3) {?>
                        <?$_i=0;$_maxchilds=12;
                        $_count=count($childs);
                        foreach ($childs as $child){
                            $_i++; if ($_i > $_maxchilds) { break; }?>
                            <li><a href="<?=$big_catalogs['data'][$child]->linkPriceList($item)?>" rel="nofollow"><?=$big_catalogs['data'][$child]->val('web_name')?></a>
                        <?}?>
                    <?} else {?>
                        <li><a href="<?=$big_catalogs['data'][$id_parent]->linkPriceList($item)?>" rel="nofollow"><?=$big_catalogs['data'][$id_parent]->name()?></a><?if($childs){?>: <?}?><?$_i=0;$_maxchilds=12;$_count=count($childs);foreach ($childs as $child){$_i++; if ($_i > $_maxchilds) { break; }?><?=$big_catalogs['data'][$child]->val('web_name')?><?if($_i == $_maxchilds && $_count > $_maxchilds){ ?>; ...<? } elseif ($_i !== $_count){?>; <?}?><?}?></li>
                    <?}?>
				<?}?>
			</ul>
			<p>Подробный и полный перечень товаров и услуг фирмы <?=$item->name()?> представлен в <a href="<?=$item->linkPricelist()?>" rel="nofollow">прайс-листе</a><br /></p>
		</div>
		<?} else {?>
		<div class="cat_description" style="margin-left: 0;">
			<p>Более детальную информацию о фирме (организации, компании), вы можете получить на ее официальном сайте, в офисе или связавшись с ее представителем по телефонам	<? if ($item->hasPhone()) {?><?= $item->phone()?><? }?></p>
		</div>
		<div class="error-info">
			<?/*Если вы являетесь владельцем или представителем данной организации, для размещения вашего прайс-листа или редактирования информации, обратитесь в региональное представительство сайта или <a class="fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/38352/10/?id_option=18" rel="nofollow">оставьте сообщение администратору</a>*/?>
			Если вы являетесь владельцем или представителем данной организации, для размещения вашего прайс-листа или редактирования информации, обратитесь в региональное представительство сайта или <a class="fancybox fancybox.ajax" href="/firm-feedback/get-error-form/<?=$item->id()?>/" rel="nofollow">оставьте сообщение администратору</a>
		</div>
		<?}?>
		<? if ($branch_names) {?>
			<div class="delimiter-line"></div> 	
			<div class="firm-bottom-block">
				<h2>Представительства и филиалы фирмы</h2>
				<?foreach ($branch_names as $name => $_branches) {?>
				<div class="branch">
					<h3><?= $name?></h3>
					<ul>
					<?  foreach ($_branches as $branch) {?>
					<li class="contacts">
						<p><a href="<?=$branch->linkItem()?>"><?=$branch->address()?></a></p>
						<? if ($branch->hasPhone()) {?><p><span class="r tel"><?= $branch->phone()?></span></p><? }?>
					</li>
					<?}?>
					</ul>
				</div>
				<? }?>
			</div>
		<?}?>
			
		<?if($item->hasDelivery()){?>
			<div class="delimiter-line"></div> 	
			<div class="firm-bottom-block cat_description">
				<h2>Условия доставки и оплаты</h2>
				<?=$delivery['text']?>
				<?if(isset($delivery['types'])){?>
				<ul>
				<?  foreach ($delivery['types'] as $k=>$v) {?>
					<li><?=$v?></li>
				<?}?>
				</ul>
				<?}?>
			</div>
		<?}?>
				
		<?if($item->hasFiles()){?>
			<div class="delimiter-line"></div> 	
			<div class="firm-bottom-block">
				<h2>Ссылки для просмотра и скачивания файлов</h2>
				<div class="uploaded-files">
					<ul>
						<?foreach($files as $img){?><li>
							<div class="img"><a rel="nofollow" href="<?=$img->link()?>"><img src="<?=$img->thumb('_s')?>" alt="" /></a></div>
							<div class="name">
								<a rel="nofollow" href="<?=$img->link()?>"><?=$img->name()?></a>
								<span><?=$img->val('file_extension')?>, <?=$img->getFormatSize("", 0)?></span>
							</div>
						</li><?}?>
					</ul>
				</div>
			</div>
		<?}?>
	</div>
</div>
<div class="reviews">
	<?if($reviews){?>
	<div class="last_reviews">
		<h2>Новые отзывы</h2>
		<a rel="nofollow" class="more_review fancybox fancybox.ajax" href="/firm-review/get-add-form/<?=$item->id()?>/">Добавить отзыв</a>
		<?foreach ($reviews as $rev){?>
		<div class="review">
			<?= app()->chunk()->setArgs([$rev['score'], true])->render('rating.stars')?>
			<div class="main_review">
				<div class="name_date">
					<span class="name"><?=$rev['user']?></span>, <span class="date"><?=$rev['date']?>:</span>
				</div>
				<p><?=strip_tags($rev['text'])?></p>
			</div>
			<?if($rev['reply_text']){?>
			<div class="main_review reply">
				<div class="name_date">
					<span class="name"><?=$rev['reply_user_name']?></span>, <span class="date"><?=$rev['reply_date']?>:</span>
				</div>
				<p><?=$rev['reply_text']?></p>
			</div>
			<?}?>
		</div>
		<?}?>
		<a class="more_review" data-page="1" href="<?=app()->linkFilter($url, ['mode' => 'review'])?>" style="float: left;">все отзывы</a>
	</div>
	<?} else {?>
	<div class="last_reviews">
		<h2>Отзывы</h2>
		<a class="more_review fancybox fancybox.ajax" href="/firm-review/get-add-form/<?=$item->id()?>/" rel="nofollow">Добавить отзыв</a>
		<a class="fancybox fancybox.ajax red-block-link" href="/firm-review/get-add-form/<?=$item->id()?>/" rel="nofollow"><div class="red-block">Для этой фирмы пока нет отзывов, поделитесь своим!</div></a>
	</div>
	<?}?>
	<div class="new_company">
		<?if($videos){?><a class="head_h2" href="<?=app()->linkFilter($url, $filters, ['mode' => 'video'])?>">Видеоблог</a><?}?>
		<div class="companies">
			<br />
			<?=app()->chunk()->set('items', $videos)->render('firm.chunk_video')?>
			<?=app()->chunk()->set('items', $questions)->render('firm.chunk_questions')?>
		</div>
	</div>
</div>
<?=$firm_on_map?>
<div class="firm-bottom-block grey">
	<div class="button_set">
		<?if($item->hasPriceList()){?><a class="price_list" href="<?= $item->linkPricelist()?>" rel="nofollow">Прайс-лист</a><?}?>
		<?if($item->hasEmail()){?><a class="write fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/<?=$item->id()?>/" rel="nofollow">Отправить сообщение</a><?}?>
		<?if($item->hasCellPhone() && $item->id_service() == 10){?><a class="cell_phone fancybox fancybox.ajax" href="/firm-feedback/get-callback-form/<?=$item->id()?>/" rel="nofollow">Заказать звонок</a><?}?>
	</div>
	<div itemscope itemtype="http://schema.org/Organization">
		<span itemprop="name"><?=$item->name()?></span>
		<?if($item->hasActivity()){?><p><span itemprop="description"><?= $item->activity()?></span></p><? }?>
		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<p><?= $item->addressWithProps()?></p>
			<?if($item->hasRegionCity()){?><p><?=$item->regionCity()?></p><?}?>
		</div>
		<?if($item->hasPhone()){?><div class="phone_set"><span itemprop="telephone"><?=$item->phone()?></span></div><? }?>
		<?if($item->hasFax()){?><div class="phone_set"><span itemprop="faxNumber"><?= $item->fax()?></span></div><? }?>
	</div>
</div>
<?if($types) {?>
	<div class="firm-catalog">
			<div class="headline">Фирма представлена в следующих разделах каталога фирм:</div>
			<div class="tags_field tags_field_duty">
			<ul>
			<?  foreach ($types as $type) {?>
				<li><a href="<?=$type->linkTag()?>"><?=$type->name()?></a></li>
			<?}?>
			</ul>
			</div>
	</div>
<?}?>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>
<?} else {?>
<div class="item_info">
	<div class="search_result">
	<?if($types) {?>
	<div class="firm-bottom-block">
		<h2>Фирма представлена в следующих разделах каталога фирм:</h2>
		<div class="tags_field tags_field_duty">
		<ul>
		<?  foreach ($types as $type) {?>
			<li><a href="<?=$type->linkTag()?>"><?=$type->name()?></a></li>
		<?}?>
		</ul>
		</div>
	</div>
	<?}?>	
	<? if ($analogs) {?>
	<div class="delimiter-line"></div> 	
	<div class="firm-bottom-block">
		<h2>Некоторые фирмы аналогичного вида деятельности <?=app()->location()->currentName('prepositional')?>:</h2>
		<?foreach ($analogs as $analog) {?>
		<div class="branch">
			<a href="<?= $analog->linkItem()?>"><?= $analog->name()?></a>
			<div class="contacts">
				<p><span class="l">Адрес:</span><?=$analog->address()?></p>
				<? if ($analog->hasPhone()) {?><p><span class="l">Телефон:</span><span class="r tel"><?= $analog->phone()?></span></p><? }?>
			</div>
		</div>
		<? }?>
	</div>
	<?}?>
	</div>
</div>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>
<?}?>