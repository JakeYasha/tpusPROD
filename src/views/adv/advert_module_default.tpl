<?if($items){?>
        <div class="search_adv_block">
		<!--noindex-->
                <span class="title">РЕКЛАМА</span>
                <div class="search_adv_block_container test">
                        <?  foreach ($items as $advert_module) {?>
                                <div class="adv_module_block">
                                        <div class="image">
                                                <?if (isset($advert_module)){?>
                                                        <?if ($advert_module->hasFullImage()){?>
                                                                <a class="fancybox" href="<?=$advert_module->getFullImage()->link()?>">
                                                                        <img alt="" src="<?=$advert_module->getFullImage()->link()?>">
                                                                </a>
                                                        <?} else {?>
                                                                <img src="<?=$advert_module->getFullImage()->link()?>">
                                                        <?}?>
                                                <?} else {?>
                                                        <img src="#">
                                                <?}?>
                                        </div>
                                        <div class="adv-text">
                                                <div class="adv_module_title">
                                                        <?if ($advert_module->hasUrl()){?><a rel="nofollow" href="#" target="_blank"><?=$advert_module->val('header')?></a><?} else {?><?=$advert_module->val('header')?><?}?>
                                                </div>
                                                <div class="adv_module_description">
                                                        <p><?=$advert_module->val('adv_text')?></p><span></span><?if ($advert_module->hasUrl()){?><p><a href=#">перейти на сайт</a></p><?}?>
                                                </div>
                                        </div>
                                </div>
                        <?}?>
                </div>
        <!--/noindex-->
		</div>
<?}?>