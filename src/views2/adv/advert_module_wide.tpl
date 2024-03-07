<?if($items){?>
        <div class="search_adv_block">
		<!--noindex-->
                <span class="title">РЕКЛАМА</span>
                <div class="search_adv_block_container">
                        <? foreach ($items as $advert_module) {?>
                                <div class="adv_module_block_wide clearfix">
                                        <div class="image">
                                                <?if (isset($advert_module)){?>
                                                        <?if ($advert_module->hasFullImage()){?>
                                                                <a class="fancybox" href="<?=$advert_module->getFullImage()->link()?>">
                                                                        <img src="<?=$advert_module->getImage()->link()?>">
                                                                </a>
                                                        <?} else {?>
                                                                <img src="<?=$advert_module->getImage()->link()?>">
                                                        <?}?>
                                                <?} else {?>
                                                        <img src="#">
                                                <?}?>
                                        </div>
                                        <div class="adv-text">
                                                <div class="adv_module_wide_title">
                                                        <div class="adv_module_wide_period">с <?= CDateTime::gmt("d.m.Y", $advert_module->val('timestamp_beginning')) ?> по <?= CDateTime::gmt("d.m.Y", $advert_module->val('timestamp_ending')) ?></div>
                                                        <span><?=$advert_module->val('header')?></span>
                                                </div>
                                                <div class="adv_module_wide_description">
                                                        <p><?=$advert_module->val('adv_text')?></p>
                                                        <div class="adv_module_wide_details">
                                                                <p>
                                                                        <?if ($advert_module->hasUrl()){?><a rel="nofollow" href="<?=$advert_module->val('url')?>" target="_blank" >Сайт</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?}?>
                                                                        <?if ($advert_module->hasMoreUrl()){?><a rel="nofollow" href="<?=$advert_module->val('more_url')?>" target="_blank" ><?=$advert_module->val('target_btn_name') == 'more' ? 'Узнать подробнее' : ($advert_module->val('target_btn_name') == 'onlineshop' ? 'В интернет-магазин' : 'Получить промокод')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?}?>
                                                                        <?if ($advert_module->hasEmail() || $advert_module->hasPhone()){?><a class="fancybox fancybox.ajax" rel="nofollow" href="/advert-module/get-request-form/?id_advert_module=<?=$advert_module->id()?>"><?=$advert_module->val('callback_btn_name') == 'order' ? 'Заказать' : 'Записаться'?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?}?>
                                                                </p>
                                                        </div>
                                                </div>
                                        </div>
                                        <?if ($advert_module->restrictions) {?>
                                                <div class="adv-restrictions">
                                                        <?=$advert_module->restrictions[0]->name()?>
                                                </div>
                                        <?}?>
                <?}?>
                        </div>
                </div>
        <!--/noindex-->
		</div>
<?}?>