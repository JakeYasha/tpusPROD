
<main>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?> 
    <div class="page__container">
        <section class="hero hero_service" style="background-image: radial-gradient(50% 50% at 50% 50%,rgba(0,0,0,.2) 0,rgba(0,0,0,.7) 100%),url(<?=$index_img?>);">
            <div class="hero__content">
                <div class="mdc-layout-grid">
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                            <h1 class="page-title page-title_service"><?= $header ?></h1>
                        </div>
                    </div>
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-layout-grid__cell">
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <p class="block-title__heading block-title__heading_number">256</p>
                                <p class="block-title__sub-heading">ГОРОДОВ РОССИИ</p>
                            </div>
                        </div>
                        <div class="mdc-layout-grid__cell">
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <p class="block-title__heading block-title__heading_number">130 000</p>
                                <p class="block-title__sub-heading">ВСЕГО ФИРМ И ОРГАНИЗАЦИЙ</p>
                            </div>
                        </div>
                        <div class="mdc-layout-grid__cell">
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <p class="block-title__heading block-title__heading_number">3 150 000</p>
                                <p class="block-title__sub-heading">ВСЕГО ПРЕДЛОЖЕНИЙ ТОВАРОВ И УСЛУГ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="mdc-layout-grid">
        <? 
        // вывод лого
        /*if ($logo_img){
            ?>
                <img src="<?=$logo_img;?>"?>
            <?
        }*/
        ?>
            
            <?= $text_bottom->val('text')?>
            
            
            <?
            /*
            Табличка фирм
            */
            
//if (APP_IS_DEV_MODE){
    if (isset($table_upper)){
    ?>
<div>
    <style>
        .tp-div-upper {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        @media (max-width: 701px) {
            .tp-div-upper {
                flex-wrap: wrap;
            }
        }
    </style>
    
    <div class="tp-div-upper">  
        <?
        if (isset($table_upper['page']) && count($table_upper['page'])>0){
        ?>
    
        <table class="tp_table_upper">
            <tr><td style="padding: 0rem 0.8rem;"><h2>Актуальные разделы:<h2></td></tr>           
            <?
                foreach ($table_upper['page'] as $yac_upper){
                    ?>
                            <tr>
                                <td style="padding: 0 1.1rem!important;">
                                    <p><a class="company_name" target="_blank" href="<?=$yac_upper['href'];?>?&ut_r=r"  title="Раздел: <?=$yac_upper['name'];?>"><?=$yac_upper['name'];?></a></p>        
                                </td>
                            </tr>      
                    <?
                }
            ?>
        </table>
        <?
        }
        ?>
        
        <?
        if (isset($table_upper['firm']) && count($table_upper['firm'])>0){
        ?>
        <table class="tp_table_upper">
            <tr><td style="padding: 0rem 0.8rem;"><h2>Фирмы месяца:<h2></td></tr>           
            <?
                foreach ($table_upper['firm'] as $yac_upper){
                    ?>
                            <tr>
                                <td style="padding: 0 1.1rem!important;">
                                    <p><a class="company_name" target="_blank" href="<?=$yac_upper['href'];?>?&ut_f=f" title="Фирма: <?=$yac_upper['name'];?>"><?=$yac_upper['name'];?></a></p>        
                                </td>
                            </tr>      
                    <?
                }
            ?>
        </table>
        <?
        }
        ?>
        <?
        if (isset($table_upper['material']) && count($table_upper['material'])>0){
        ?>
        <table class="tp_table_upper">
            <tr><td style="padding: 0rem 0.8rem;"><h2>Интересные статьи:<h2></td></tr>           
            <?
                foreach ($table_upper['material'] as $yac_upper){
                    ?>
                            <tr>
                                <td style="padding: 0 1.1rem!important;">
                                    <p><a class="company_name" target="_blank" href="<?=$yac_upper['href'];?>?&ut_n=n"  title="Статья: <?=$yac_upper['name'];?>"><?=$yac_upper['name'];?></a></p>        
                                </td>
                            </tr>      
                    <?
                }
            ?>
        </table>
        <?
        }
        ?>
        
    </div>
</div>   
        <?
    //var_dump($table_upper);
    }
//}
?>
            
            
            
            
            
            
            
            
            
            
            
            
            
            <?= app()->chunk()->render('adv.header_banners_slider') ?>
            <div class="block-title module">
                <div class="block-title__decore"></div>
                <h2 class="block-title__heading"><a href="<?= app()->link('/advert-module/')?>">СПЕЦПРЕДЛОЖЕНИЯ, СКИДКИ И АКЦИИ <?= app()->location()->currentName('prepositional') ?></a></h2>
            </div>
            <div class="special module">
                <!--advmdehh-->
                <?=app()->chunk()->render('advtext.advert_module_admitadbox');?>
                <div class="mdc-layout-grid__inner">
                    <?//= $advert_modules ?>
                    <?= $index_promo ?>
                </div>
            </div>
            <?= $last_reviews?>
            <? /*
            <!--div class="articles module">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-3-desktop">
                        <aside class="sidebar">
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <h2 class="block-title__heading">РУБРИКАТОР</h2>
                            </div>
                            <ul class="list sidebar-list">
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">ВСЕ</a></li>
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">КРАСОТА И ЗДОРОВЬЕ</a></li>
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">РЕСТОРАНЫ И КАФЕ</a></li>
                            </ul>
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <h2 class="block-title__heading">ПОДБОРКИ</h2>
                            </div>
                            <ul class="list sidebar-list">
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">НОВЫЙ ГОД</a></li>
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">РЕЦЕПТЫ ОТ ШЕФ-ПОВАРА</a></li>
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">ДАЧНЫЙ СЕЗОН</a></li>
                            </ul><a href=""><img class="img-fluid" src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"></a>
                        </aside>
                    </div>
                    <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                        <div class="block-title">
                            <div class="block-title__decore"></div>
                            <h2 class="block-title__heading">Новые публикации</h2>
                        </div>
                        <div class="articles module">
                            <article class="article">
                                <div class="mdc-layout-grid__inner">
                                    <div class="mdc-layout-grid__cell--span-6-desktop w-100-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-1">
                                        <div class="article__img"><img class="img-fluid" src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"><span class="article__tag">ЭКОНОМИКА</span></div>
                                    </div>
                                    <div class="mdc-layout-grid__cell--span-6-desktop w-100-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-2"><a class="article__heading" href="">Эксперт: Свободные деньги у россиян появились из-за утраты оптимизма</a>
                                        <p class="article__text">Аналитик Центра Аналитических исследований Анатолий Петров считает, что свободные деньги у россиян появились из-за утраты оптимизма.</p>
                                        <div class="article__info">Источник</div>
                                    </div>
                                </div>
                            </article>
                            <article class="article">
                                <div class="mdc-layout-grid__inner">
                                    <div class="mdc-layout-grid__cell--span-6-desktop w-100-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-1 mdc-layout-grid__cell--order-md-2"><a class="article__heading" href="">Эксперт: Свободные деньги у россиян появились из-за утраты оптимизма</a>
                                        <p class="article__text">Аналитик Центра Аналитических исследований Анатолий Петров считает, что свободные деньги у россиян появились из-за утраты оптимизма.</p>
                                        <div class="article__info">Источник</div>
                                    </div>
                                    <div class="mdc-layout-grid__cell--span-6-desktop w-100-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-2 mdc-layout-grid__cell--order-md-1">
                                        <div class="article__img"><img class="img-fluid" src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"><span class="article__tag">ЭКОНОМИКА</span></div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                        <aside class="sidebar sidebar__right">
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <h2 class="block-title__heading">горячие новости</h2>
                            </div>
                            <ul class="list sidebar-list">
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">Адвокат: FIFA накажет оргкомитет ЧМ-2018 за пробежку Pussy Riot по полю</a></li>
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">Адвокат: FIFA накажет оргкомитет ЧМ-2018 за пробежку Pussy Riot по полю</a></li>
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="">Адвокат: FIFA накажет оргкомитет ЧМ-2018 за пробежку Pussy Riot по полю</a></li>
                                <li class="sidebar-list__item sidebar-list__item_with-img"><img class="img-fluid" src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"><a class="sidebar-list__link" href="">Адвокат: FIFA накажет оргкомитет ЧМ-2018 за пробежку Pussy Riot по полю</a></li>
                            </ul>
                            <div class="view-all"><a class="share" href="">Все горячие новости<img src="/img3/next.png"></a></div>
                            <div class="block-title module">
                                <div class="block-title__decore"></div>
                                <h2 class="block-title__heading">афиша города</h2>
                            </div>
                            <a class="subscribe-card promo-card" href="" style="margin-top: 1.5rem; background-image: url(https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60)">
                                <div class="article__tag article__tag_promo">афиша</div>
                                <div class="promo-card__content">
                                    <h4>Праздник в «Доме еврейского народа» на Маркса</h4>
                                    <p>В Доме еврейского народа состоится праздник по случаю еврейского праздника.</p>
                                </div>
                            </a>
                            <div class="view-all module"><a class="share" href="">Все мероприятия<img src="/img3/next.png"></a></div>
                            <a class="module banner-link" href=""><img class="img-fluid" src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"></a>
                        </aside>
                    </div>
                </div>
            </div-->*/?>
        </div>
    </div>
</main>

<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
?>

