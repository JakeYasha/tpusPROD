<?if(!$item->isBlocked()){?>
<div class="item_info offset-none">
	<div class="search-result">
		<?= $tabs?>
		<?if($item->hasAbout()){?>
		<div class="firm-bottom-block cat_description">
			<?=$item->about()?>
            <!--5555-->
		</div>
		<?}?>
        
        <? if ($item->dopcontent()){
            ?>
                <style>
                    .dop_content_info>img{
                        max-width:100%;
                    }
                </style>
                <div style="margin-top:5px;margin-bottom:5px;" class="dop_content_info">
                    <?=$item->dopcontent();?>
                </div>
            <?
            }
            ?>
		<?if($item->hasDescription()){?>
		<div class="firm-bottom-block cat_description">
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
		<div class="firm-bottom-block cat_description">
			<h2>Каталог предложений</h2>
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
		</div>	
		<div class="search-result-element-block firm-wrapper" style="padding: 0; border-bottom: none;">
			<div class="element-info-block for-firm" style="padding: 0;">
				<div class="buttons-block">
					<a class="btn-base btn-red" href="<?=$item->linkPricelist()?>" rel="nofollow">Посмотреть прайс-лист</a>
				</div>
			</div>
		</div>
		<?} else {?>
		<div class="firm-bottom-block cat_description">
			<p>Более детальную информацию о фирме (организации, компании), вы можете получить на ее официальном сайте, в офисе или связавшись с ее представителем по телефонам	<? if ($item->hasPhone()) {?><?= $item->phone()?><? }?></p>
		</div>
		<div class="error-info">
			<?/*Если вы являетесь владельцем или представителем данной организации, для размещения вашего прайс-листа или редактирования информации, обратитесь в региональное представительство сайта или <a class="fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/38352/10/?id_option=18" rel="nofollow">оставьте сообщение администратору</a>*/?>
			Если вы являетесь владельцем или представителем данной организации, для размещения вашего прайс-листа или редактирования информации, обратитесь в региональное представительство сайта или <a class="fancybox fancybox.ajax" href="/firm-feedback/get-error-form/<?=$item->id()?>/" rel="nofollow">оставьте сообщение администратору</a>
		</div>
		<?}?>
		<? if ($branch_names && ($branch_names['branches'])) { /* || $branch_names['firm_branches'])) {*/ ?>
			<div class="delimiter-line"></div>
            <div class="firm-bottom-block cat_description">
                <h2>Представительства и филиалы фирмы</h2>
                <div class="search-result-element-block firm-wrapper firm-branches">
                    <div class="element-info-block for-firm">
                        <a href="#" class="js-show-contacts btn-base btn-grey show-contacts" data-firm-id="<?=$item->id()?>"><span class="show-contacts-text">Филиалы фирмы</span></a>
                        <div class="firm-contacts real-contacts js-show-contacts-wrapper">
                            <?foreach ($branch_names['branches'] as $name => $_branches) {?>
                                <div class="branch"><h3><?= $name?></h3></div>
                                <?  foreach ($_branches as $branch) {?>
                                    <div class="delimiter-line"></div> 	
                                    <div class="firm-contacts-line">
                                        <span class="contact-type">Адрес:</span>
                                        <span><?= $branch->address()?></span>
                                    </div>
                                    <? if ($branch->hasPhone()) {?>
                                        <div class="firm-contacts-line">
                                            <span class="contact-type">Телефон:</span>
                                            <span><?= $branch->renderPhoneLinks()?></span>
                                        </div>
                                    <? }?>
                                <? }?>
                            <? }?>	
                            <?/*foreach ($branch_names['firm_branches'] as $name => $_branches) {?>
                                <div class="branch"><h3><?= $name?></h3></div>
                                <?  foreach ($_branches as $branch) {?>
                                    <div class="delimiter-line"></div> 	
                                    <div class="firm-contacts-line firm_branch">
                                        <span class="contact-type">Адрес:</span>
                                        <span><?= $branch->address()?></span>
                                    </div>
                                    <? if ($branch->hasPhone()) {?>
                                        <div class="firm-contacts-line">
                                            <span class="contact-type">Телефон:</span>
                                            <span><?= $branch->renderPhoneLinks()?></span>
                                        </div>
                                    <? }?>
                                <? }?>
                            <? }*/?>	
                        </div>
                    </div>
                </div>		
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
			<div class="main_review">
                <?= app()->chunk()->setArgs([$rev['score'], true])->render('rating.stars')?><br/>
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
<?if((isset($firm_on_map) && $firm_on_map) || (isset($firm_branches_on_map) && $firm_branches_on_map)) {?>
    <?=$firm_on_map?>
    <?=$firm_branches_on_map?>
    <div class="map_field">
        <h2>Карта проезда:</h2>
        <?if($item->hasPath()){?>
            <div class="firm-bottom-block">
                <p>Проезд (ориентир):</p>
                <pre><?=str_replace("\\\r", "\n", $item->path())?></pre>
            </div>
        <?}?>
        <div id="map"></div>
    </div>
<?}?>
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
    <div class="search-result">
		<h2>Поделись информацией:</h2>
		<div class="firm-contacts-line share-block">
			<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
			<script src="//yastatic.net/share2/share.js"></script>
			<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,viber,whatsapp,skype,telegram"></div>
		</div>
		<div class="error-info">
            Если вы увидели неточность в представленных данных по фирме <?= $item->name() ?>, <a class="fancybox fancybox.ajax" href="/firm-feedback/get-error-form/<?= $item->id_firm() ?>/<?= $item->id_service() ?>/" rel="nofollow">сообщите об ошибке</a>. Мы уточним информацию о фирме и внесем изменения в данные.
        </div>
    </div>


<?} else {?>
<div class="item_info">
	<div class="search-result">
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
<?}?>