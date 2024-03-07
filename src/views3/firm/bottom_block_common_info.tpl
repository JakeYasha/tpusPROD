<?if(!$item->isBlocked()){?>
    <div class="mdc-layout-grid__inner">
        
        <div class="mdc-layout-grid__cell--span-12">
            <?= $tabs?>
            <!--div class="brand-list__actions brand-list__actions_desktop">
                <a class="btn brand-list__action btn_primary" href="">Общая информация</a>
                <a class="btn brand-list__action btn_outline btn_outline--secondary" href="">Прайс лист</a>
            </div-->
            <?if($item->hasAbout()){?>
                <div class="tp_firm_about_info"><?=$item->about()?></div>
            <?}?>
            <?if($item->hasDescription()){?>
                <h2>Дополнительная информация</h2>
                <p><?=$item->description()?></p>
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
                <h2>Каталог предложений</h2>
                <a class="btn brand-list__action btn_outline btn_outline--primary" href="<?=$item->linkPricelist()?>"rel="nofollow">Все предложения</a> 
                <p>Некоторые группы товаров и услуг из прайс-листа фирмы:</p>
                <ul class="list read-more__list product-list">
                    <?foreach ($big_catalogs['matrix'] as $id_parent => $childs) {?>
                        <?if ($big_catalogs['data'][$id_parent]->val('node_level') < 3) {?>
                            <?$_i=0;$_maxchilds=12;
                            $_count=count($childs);
                            foreach ($childs as $child){
                                $_i++; if ($_i > $_maxchilds) { break; }?>
                                <li class="read-more__list--item"><p><a href="<?=$big_catalogs['data'][$child]->linkPriceList($item)?>" rel="nofollow"><?=$big_catalogs['data'][$child]->val('web_name')?></a></p></li>
                            <?}?>
                        <?} else {?>
                            <li class="read-more__list--item"><p><a href="<?=$big_catalogs['data'][$id_parent]->linkPriceList($item)?>" rel="nofollow"><?=$big_catalogs['data'][$id_parent]->name()?></a><?if($childs){?>: <?}?><?$_i=0;$_maxchilds=12;$_count=count($childs);foreach ($childs as $child){$_i++; if ($_i > $_maxchilds) { break; }?><?=$big_catalogs['data'][$child]->val('web_name')?><?if($_i == $_maxchilds && $_count > $_maxchilds){ ?>; ...<? } elseif ($_i !== $_count){?>; <?}?><?}?></p></li>
                        <?}?>
                    <?}?>
                </ul>
            <?} else {?>
                <p>Более детальную информацию о фирме (организации, компании), вы можете получить на ее официальном сайте, в офисе или связавшись с ее представителем по телефонам	<? if ($item->hasPhone()) {?><?= $item->phone()?><? }?></p>
                <div class="module stat-info_v2">
                    Если вы являетесь владельцем или представителем данной организации, для размещения вашего прайс-листа или редактирования информации, обратитесь в региональное представительство сайта или <a class="js-open-modal-ajax" data-target="feedbackAddErrorForm" href="" data-url="/firm-feedback/get-error-form/<?=$item->id()?>/" rel="nofollow">оставьте сообщение администратору</a>
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
	<div class="divider"></div>
    
    <div class="mdc-layout-grid__cell--span-12">
        <?=app()->chunk()->set('items', $questions)->render('firm.chunk_questions')?>
    </div>
    <div class="comments module">
        <?if($reviews){?>
            <div class="form_comment">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--align-middle">
                        <h2>Отзывы</h2>
                    </div>
                    <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                        <a rel="nofollow" class="btn btn_primary btn_comments js-open-modal-ajax" href="#" data-target="reviewAddForm" data-url="/firm-review/get-add-form/<?=$item->id()?>/">Добавить отзыв</a>
                    </div>
                </div>
            </div>
            <a class="comments__counter module" data-page="1" href="<?=app()->linkFilter($url, ['mode' => 'review'])?>">Отзывы<?=isset($reviews_count) && (int)$reviews_count ? ' (' . (int)$reviews_count . ')' : '' ?></a>
            <ul class="list comments-list">
                <?foreach ($reviews as $rev){?>
                    <li class="comments-list__item">
                        <div class="comments-list__profile--img">
                            <img src="/css/img/no_img.png" alt="Нет фотографии">
                        </div>
                        <div class="comment-list-content">
                            <div class="comments-list__profile--info">
                                <span class="comments-list__profile--name"><?=$rev['user']?></span>
                                <span class="comments-list__time"><?=$rev['date']?></span>
                                <p class="comments-list__text"><?=strip_tags($rev['text'])?></p>
                            </div>
                        </div>
                    </li>
                <?}?>
            </ul>
        <?} else {?>
            <div class="form_comment">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--align-middle">
                        <h2>Отзывы</h2>
                    </div>
                    <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                        <a rel="nofollow" class="btn btn_primary btn_comments js-open-modal-ajax" href="#" data-target="reviewAddForm" data-url="/firm-review/get-add-form/<?=$item->id()?>/">Добавить отзыв</a>
                    </div>
                </div>
            </div>
        <?}?>
    </div>
    <?if($videos){?>
        <div class="divider"></div>
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell--span-12">
                <h2><a href="<?=app()->linkFilter($url, $filters, ['mode' => 'video'])?>">Видеоблог</a></h2>
            </div>
        </div>
    <?}?>
    <div class="special module">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
                <br />
                <?=app()->chunk()->set('items', $videos)->render('firm.chunk_video')?>
                
            </div>
        </div>
    </div>
    <div class="divider"></div>

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
    <div class="divider"></div>
    <h4>Фирма представлена в следующих разделах каталога фирм:</h4>
    <ul class="list read-more__list product-list">
        <?foreach ($types as $type) {?>
            <li class="read-more__list--item"><p><a href="<?=$type->linkTag()?>"><?=$type->name()?></a></p></li>
        <?}?>
    </ul>
<?}?>
<div class="divider"></div>
<div class="search-result">
    <h2>Поделись информацией:</h2>
    <div class="firm-contacts-line share-block">
        <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
        <script src="//yastatic.net/share2/share.js"></script>
        <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,viber,whatsapp,skype,telegram"></div>
    </div>
    <div class="module stat-info">
        Если вы увидели неточность в представленных данных по фирме <?= $item->name() ?>, <a class="fancybox fancybox.ajax button_red js-open-modal-ajax" href="/firm-feedback/get-error-form/<?= $item->id_firm() ?>/<?= $item->id_service() ?>/" rel="nofollow">сообщите об ошибке</a>. Мы уточним информацию о фирме и внесем изменения в данные.
    </div>
</div>
<br/>
<?} else {?>
    <div class="divider"></div>
    <?if($types) {?>
        <h4>Фирма представлена в следующих разделах каталога фирм:</h4>
        <ul class="list read-more__list product-list">
            <?foreach ($types as $type) {?>
                <li class="read-more__list--item"><p><a href="<?=$type->linkTag()?>"><?=$type->name()?></a></p></li>
            <?}?>
        </ul>
    <?}?>	
    <? if ($analogs) {?>
        <div class="divider"></div>
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
<?}?>