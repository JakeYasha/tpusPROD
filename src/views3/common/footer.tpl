
<!-- Paste next code to the place where in-read ad should appear -->
<div class="page__container">
    <div class="tgx-rlf" data-rlf-id="5325" data-rlf-auto="1" data-rlf-flt="1" data-rlf-dock="0" data-rlf-align="rb" data-rlf-min-time="60"></div>
</div>


<!-- Paste next line before closing BODY tag -->


<footer class="footer">
    <div class="page__container">
        <div class="mdc-layout-grid">
            <?= app()->chunk()->render('common.footer_menu') ?>
            <div class="footer__hr"></div>
            <div class="footer-sub">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                        <div class="site-brand">TovaryPlus.ru - <?=app()->location()->currentName()?></div>
                        <div class="social">
                            <!--noindex-->
                            <?
                            $can_show = false;
                            ?>
                            <? if ($service->hasFBLink() && $can_show) { ?><a class="social__link" target="_blank" href="<?= $service->getFBLink() ?>" title="<?= CHtml::encode($service->name()) ?> в Facebook" rel="nofollow"><img alt="Facebook" src="/img3/fb.png"></a><?}?>
                            <? if ($service->hasVKLink()) { ?><a class="social__link" target="_blank" href="<?= $service->getVKLink() ?>" title="<?= CHtml::encode($service->name()) ?> в ВКонтакте" rel="nofollow"><img alt="ВКонтакте" src="/img3/vk.png"></a><?}?>
                            <? if ($service->hasINLink() && $can_show) { ?><a class="social__link" target="_blank" href="<?= $service->getINLink() ?>" title="<?= CHtml::encode($service->name()) ?> в Instagram" rel="nofollow"><img alt="Instagram" src="/img3/inst.png"></a><?}?>
                            <? if ($service->hasTWLink()) { ?><a class="social__link" target="_blank" href="<?= $service->getTWLink() ?>" title="<?= CHtml::encode($service->name()) ?> в Twitter" rel="nofollow"><img alt="Twitter" src="/img3/twitter.png"></a><?}?>
                            <? if ($service->hasOKLink()) { ?><a class="social__link" target="_blank" href="<?= $service->getOKLink() ?>" title="<?= CHtml::encode($service->name()) ?> в Однокласниках" rel="nofollow"><img alt="Одноклассники" src="/img3/ok.png"></a><?}?>
                            <? if ($service->hasGPLink()) { ?><a class="social__link" target="_blank" href="<?= $service->getGPLink() ?>" title="<?= CHtml::encode($service->name()) ?> в Google+" rel="nofollow"><img src="/img3/g+.png"></a><?}?>
                            <? if ($service->hasYTLink()) { ?><a class="social__link" target="_blank" href="<?= $service->getYTLink() ?>" title="<?= CHtml::encode($service->name()) ?> в YouTube" rel="nofollow"><img alt="Youtube" src="/img3/youtube.png"></a><?}?>
                            <a class="social__link" href="https://t.me/tovaryplus" target="_blank" title="Товары+ в Telegram" rel="nofollow"><img alt="Telegram" src="/img3/tg.png"></a>
                            <!--noindex-->
                        </div>
                        <ul class="list">
                            <li class="list__item"><span class="list__item--label">Региональное представительство:</span><span><a href="/ratiss/service/<?=$service->id()?>/" style="/*color: #fff;*/"><?=$service->name()?></a></span></li>
                            <?if ($service->hasAddress()) { ?><li class="list__item"><span class="list__item--label">Адрес:</span><span><?=$service->val('address')?></span></li><?}?>
                            <?if ($service->hasPhone()) { ?><li class="list__item"><span class="list__item--label">Телефон:</span>

                                <span><?
                                    if ($service->id()=='10'){
                                    ?>
                                        <a href="tel:84852259793">8 (4852) 25-97-93 </a>
                                    <?
                                    }else{
                                        echo $service->val('phone');
                                    }
                                    ?></span>
                            </li><?}?>
                            <?if ($service->hasModeWork()) { ?><li class="list__item"><span class="list__item--label">Режим работы:</span><span><?=$service->val('mode_work')?></span></li><?}?>
                            <li class="list__item"><span class="list__item--label">Email:</span><span><a href="mailto:<?=$service->val('email')?>"><?=$service->val('email')?></a></span></li>
                        </ul>
                    </div>
                    <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                        <p class="site-info">Хотите разместить на сайте информацию о своей фирме? Оставьте заявку</p><a class="btn btn_footer" href="/request/add/" rel="nofollow">Заявка на размещение рекламы</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="counters">
		<?=app()->chunk()->render('counters.metrika')?>
		<?=app()->chunk()->render('counters.google')?>
		<?=app()->chunk()->render('counters.liveinternet')?>
	</div>
    </div>
	
    <!-- VK -->
    <script>(window.Image ? (new Image()) : document.createElement('img')).src='https://vk.com/rtrg?p=VK-RTRG-156769-7sJLI';</script>
    <!-- /VK -->
    
<?
//if (APP_IS_DEV_MODE){
if (app()->location()->currentId()=="76004"){
    echo "<!--YAR-->";
                                ?>
<!-- Yandex.Metrika counter -->
<!-- Yaroslavl -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(80530753, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/80530753" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
        <?
}
//}

?>
    
</footer>

<?/*
    <script>
        function refresh_loc(){
            location = location;
        }
        window.onload = function() {
            if ((document.querySelectorAll('a[href="/materials/"]').length==0) && (document.querySelectorAll('img[alt="Корзина"]').length>0)){
                
                fetch(location, {
                    referrer: "" // не ставить заголовок Referer
                });
                setCookie('theme_name','telemagic',7);
                setTimeout(refresh_loc(),1000)
                
            }
        }
    </script>
    */?>


<script defer src="https://cdn.adlook.me/js/rlf.js"></script>
