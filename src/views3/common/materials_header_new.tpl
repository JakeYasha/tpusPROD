
<div class="header-fixed" style="width:100%;">
    <div class="page__container">
        <div class="top-line-info">
            <div class="mdc-layout-grid">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--align-middle">
                        <div class="top-line-info__place">
                            <img alt="" src="/img3/place.svg"/>
                            <span class="place-label">Ваш регион:&nbsp;</span>
                            <? if (!empty($topCities)) {?>
                                <? $current = app()->location()->city()->id(); ?>
                                <span data-city-id="<?=$current?>" class="js-open-modal js-selected-city" data-target="citySelect"> <?= app()->location()->currentName()?></span>
                            <? }?>
                        </div>
                    </div>
                    <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--align-middle mdc-layout-grid__cell--span-4-tablet">
                        <div class="top-line-info__profile">
                            <!--a href="">Регистрация</a-->
                            <? if (app()->firmManager()->exists()) { ?>	
                                <a href="/firm-manager/" rel="nofollow" class="entry"><?= app()->firmManager()->val('email') ?></a>
                                <a href="/firm-manager/logout/" rel="nofollow" class="entry-exit">Выход</a>
                            <? } elseif (app()->firmUser()->exists()) { ?>
                                <a href="/firm-user/" rel="nofollow" class="entry"><?= app()->firmUser()->val('email') ?></a>
                                <a href="/firm-user/logout/" rel="nofollow" class="entry-exit">Выход</a>
                            <? } else { ?>
                                <a href="#" class="js-open-modal-ajax" rel="nofollow" data-target="loginForm" data-url="/firm-user/get-login-form/">Войти в кабинет</a>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="top-line top-line-material">
            <div class="top-line__left">
                <button class="menu-icon"><span class="menu-icon__item"></span><span class="menu-icon__item"></span><span class="menu-icon__item"></span></button><a class="logo" href="<?= app()->link('/')?>"><img class="logo__img img-fluid" src="/img3/logo.png"></a>
            </div>
            <div class="top-line__middle">
            <div class="ya-site-form ya-site-form_inited_no" data-bem="{&quot;action&quot;:&quot;https://yandex.ru/search/site/&quot;,&quot;arrow&quot;:false,&quot;bg&quot;:&quot;transparent&quot;,&quot;fontsize&quot;:16,&quot;fg&quot;:&quot;#000000&quot;,&quot;language&quot;:&quot;ru&quot;,&quot;logo&quot;:&quot;rb&quot;,&quot;publicname&quot;:&quot;Поиск по сайту tovaryplus.ru&quot;,&quot;suggest&quot;:true,&quot;target&quot;:&quot;_blank&quot;,&quot;tld&quot;:&quot;ru&quot;,&quot;type&quot;:2,&quot;usebigdictionary&quot;:true,&quot;searchid&quot;:2582648,&quot;input_fg&quot;:&quot;#000000&quot;,&quot;input_bg&quot;:&quot;#ffffff&quot;,&quot;input_fontStyle&quot;:&quot;normal&quot;,&quot;input_fontWeight&quot;:&quot;normal&quot;,&quot;input_placeholder&quot;:&quot;Поиск по газете Товары+&quot;,&quot;input_placeholderColor&quot;:&quot;#333333&quot;,&quot;input_borderColor&quot;:&quot;#bf3745&quot;}"><form action="https://yandex.ru/search/site/" method="get" target="_blank" accept-charset="utf-8"><input type="hidden" name="searchid" value="2582648"/><input type="hidden" name="l10n" value="ru"/><input type="hidden" name="reqenc" value=""/><input type="search" name="text" value=""/><input type="submit" value="Найти"/></form></div><style type="text/css">.ya-page_js_yes .ya-site-form_inited_no { display: none; }</style><script type="text/javascript">(function(w,d,c){var s=d.createElement('script'),h=d.getElementsByTagName('script')[0],e=d.documentElement;if((' '+e.className+' ').indexOf(' ya-page_js_yes ')===-1){e.className+=' ya-page_js_yes';}s.type='text/javascript';s.async=true;s.charset='utf-8';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//site.yandex.net/v2.0/js/all.js';h.parentNode.insertBefore(s,h);(w[c]||(w[c]=[])).push(function(){Ya.Site.Form.init()})})(window,document,'yandex_site_callbacks');</script>
                <?/*
                <form action="<?= $search_settings['action'] ?>" method="get" class="js-form js-main-search-frm form-search form" data-city="<?= $search_settings['city'] ?>" data-mode="<?= $search_settings['mode'] ?>">
                    <button class="btn btn_search" type="submit"><img alt="Поиск" src="/img3/search.png"></button>
                    <div class="modal__item modal__item_search_type">
                        <select name="" class="def form__control form__control_modal js-search-type-selector">
                            <option data-mode="price" data-name="Товары и услуги">Товары и услуги</option>
                            <option data-mode="firms" data-name="Компании">Компании</option>
                            <option data-mode="news" data-name="Новости">Новости</option>
                        </select>
                    </div>
                    <?= $autocomplete ?>
                    <!--input class="form__control form__control--search-input" placeholder="Введите название товара или услуги"-->
                </form>
                <button class="btn btn_primary js-add-company" data-url="/request/add/">Добавить фирму</button>
                */?>
            </div>
            <?= app()->chunk()->render('cart.preview') ?>
        </div>
        <div class="mobile-menu">
            <button class="close-menu">✖</button>
            <div class="top-line-info__place"><img alt="" src="/img3/place.svg">
                <div class="place-label">Ваш регион: </div>
                <? $current = app()->location()->city()->id(); ?>
                <div style="width: 100%" data-city-id="<?=$current?>" class="js-open-modal js-selected-city" data-target="citySelect"><?= app()->location()->currentName()?></div>
            </div>
            <div class="module-menu__container">
                <?= $mobile_rubrics ?>
            </div>
        </div>
        <? if (!empty($topCities)) {?>
            <div class="modal" id="citySelect">
                <div class="modal__overlay"></div>
                <div class="modal__wrap">
                    <div class="modal__content">
                        <div class="modal__header">
                            <div class="modal__title">
                                Ваш регион
                            </div>
                            <button class="modal__close">
                                <i class="material-icons">
                                    close
                                </i>
                            </button>
                        </div>
                        <div class="modal__body">
                            <div class="modal__item">
                                <ul class="city-list">
                                    <? $i=0;foreach ($topCities as $city) { ?>
                                        <li <?$current == $city->id() ? 'selected="selected"' : ''?> class="city-list__item" data-city-id="<?= $city->locationId()?>">
                                            <!--noindex--><a rel="nofollow" href="/utils/change-location/<?= $city->locationId()?>/"> <?= $city->name()?></a><!--/noindex-->
                                        </li>
                                    <? } ?>
                                </ul>
                            </div>
                            <div class="modal__item">
                                <input id="sidebar-town-autocomplete" class="form__control form__control_modal js-autocomplete" type="text" data-field-name="name" data-model-alias="sts-city" data-val-mode="id" data-settings="" data-name="code" placeholder="Введите название города">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?}?>
    </div>
    <div class="page__container">
        <?= $rubrics?>
    </div>
</div>
<header class="header">
    <?//= app()->chunk()->render('adv.index_banners') //НЕ ВЫВОДИМ ВЕРХНИЕ БАННЕРЫ С РЕКЛАМОЙ!!!?>
    <? /* = app()->chunk()->render('common.header_tp_logo') */ ?>
</header>
