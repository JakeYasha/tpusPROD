<div class="search_result">
        <div class="search_adv_block">
                <span class="title">РЕКЛАМА</span>
                <div class="search_adv_block_container">
                        <div class="adv_module_block_wide clearfix">
                                <div class="image">
                                        <?if (isset($item)){?>
                                                <?if ($item->hasFullImage()){?>
                                                        <a class="fancybox" href="<?=$item->getFullImage()->link()?>">
                                                                <img alt="" src="<?=$item->getImage()->link()?>">
                                                        </a>
                                                <?} else {?>
                                                        <img alt="" src="<?=$item->getImage()->link()?>">
                                                <?}?>
                                        <?} else {?>
                                                <img alt="" src="#">
                                        <?}?>
                                </div>
                                <div class="adv-text">
                                        <div class="adv_module_wide_title">
                                                <span><?=$item->val('header')?></span>
                                        </div>
                                        <div class="adv_module_wide_description">
                                                <p><?=$item->val('adv_text')?></p>
                                        </div>
                                        <div class="adv_module_wide_details">
                                                <p><a rel="nofollow" href="#" target="_blank" >Подробнее о предложении</a><?if ($item->hasUrl()){?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a rel="nofollow" href="#" target="_blank" >Сайт...</a><?}?></p>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>