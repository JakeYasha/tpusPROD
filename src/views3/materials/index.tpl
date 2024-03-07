<main>

    <?
    if (isset($position['top'])) {
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?> 
    <div class="page__container">
        <section class="hero hero_main" style="background-image: radial-gradient(50% 50% at 50% 50%,rgba(0,0,0,.2) 0,rgba(0,0,0,.7) 100%),url(<?= $index_img ?>);margin-bottom: 2rem;">
            <div class="hero__content">
                <div class="mdc-layout-grid">
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                            <h1 class="page-title"><?= $title ?></h1>
                        </div>
                        <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                            <div class="hero__modules" style="flex-wrap: wrap;">
                                <? if (isset($temp)) { ?>

                                    <div class="hero_up_line">
                                        <b>Температура: </b><span><?=(int)$temp["result"]["temp"]; ?></span>
                                    </div>
                                    <div class="hero_up_line">
                                        <b>Погода: </b><span><?= $temp["result"]["description"]; ?></span>
                                    </div>


                                <? } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="mdc-layout-grid">
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid__cell--span-3-desktop">
                    <aside class="sidebar">
                        <? if ($rubrics) { ?>
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <h1 class="block-title__heading">ВЫПУСКИ</h1>
                                <small>Выпуски газеты - 2022-2023</small>
                                <div class="article__img">
                                    <a class="article__heading article-card__heading" href="/materials/gazeta/">
                                        <img class="img-fluid" src="/public/img3/newspapperimg.png">
                                    </a>
                                    <span class="article__tag">Смотреть</span>
                                </div>
                            </div>
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <h2 class="block-title__heading">РУБРИКАТОР</h2>
                            </div>
                            <ul class="list sidebar-list">
                                <li class="sidebar-list__item"><a class="sidebar-list__link" href="/materials/">ВСЕ</a><span><?= $rubrics_count['all']; ?></span></li>
                                <?
                                foreach ($rubrics as $rubric) {

                                    if ($rubrics_count[$rubric->id()] !== '0') {
                                        ?>
                                        <li class="sidebar-list__item"><a class="sidebar-list__link<?= $current_rubric && $current_rubric->id() === $rubric->id() ? ' active' : '' ?>" href="<?= $rubric->linkItem('materials') ?>"><?= $rubric->name() ?></a><span><?= $rubrics_count[$rubric->id()]; ?></span></li>
                                        <?
                                    }
                                }
                                ?>
                            </ul>
                            <?
                        }
                        ?>
                            <div class="tp-mt-btn-addidea"><?
                                if (APP_IS_DEV_MODE) {
                                ?>
                                <a class="tp-mt-btn tmb-red" href="javascript:void(0);" onclick="toggle_modal('IdeaAdd');">ПОДПИСАТЬСЯ НА НОВОСТИ</a>
                                
                                    <a href="#" class="js-open-modal-ajax js-open-initialized" rel="nofollow" data-target="IdeaAdd" data-url="/firm-user/get-login-form/">Войти в кабинет</a>
                                <?
                                }
                                ?>
                            </div>
                            <script>
                                function toggle_modal(id, time = 300) {
                                    $('#' + id).fadeToggle(time);
                                    if ($("body").hasClass("fixed")){
                                        $("body").removeClass("fixed");
                                    }else{
                                        $("body").addClass("fixed");
                                    }
                                    
                                }
                            </script>
                            <div class="modal js-modal-ajax" id="IdeaAdd" style="">
                                <div class="modal__overlay"></div>
                                <div class="modal__wrap">
                                    <div class="modal__content">
                                        <div class="modal__header">
                                            <div class="modal__title">
                                                <h2>Подписка на новости</h2>
                                            </div>
                                            <button class="modal__close" onclick="toggle_modal('IdeaAdd');"><i class="material-icons">close</i></button>
                                        </div>
                                        <div class="modal__body">
                                            
                                            <div class="article__info">Оставьте Ваш email и мы будем рады уведомлять Вас о новостях газеты Товары+ Всегда Вам рады!</div>
                                            <div class="modal__item">
                                                Ваш emаil:                            
                                                <input class="e-text-field form__control form__control_modal" name="email_addr" type="text" id="email_addr">                        
                                            </div>
                                            <div class="modal__item">
                                            </div>
                                            <div class="modal__item">
                                                <div class="error-submit"></div>
                                            </div>
    <a class="e-button send js-ajax-send btn btn_primary" href="javascript:void(0);" type="button">Подписаться</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        
                    </aside>
                </div>

                <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                    
                
                    <div class="block-title">
                        <div class="block-title__decore"></div>
                        <h2 class="block-title__heading">Новые публикации</h2>

<? if (isset($alert_last) && $alert_last) { ?><small><?= $alert_last; ?></small><? } ?>
                    </div>
                    <div class="articles module tp-mt-order-nth">
<? foreach ($last_materials as $last_material) { ?>
                            <article class="article tp-mt-order-nth">
                                <div class="mdc-layout-grid__inner tp-mt-flex tp-mt-flex-nw">
                                    <div class="tp-mt-materials-article-imgbox tp-mt-order-1">
                                        <div class="article__img">
                                            <a class="article__heading" href="<?= $last_material['link'] ?>" onclick="ym(1383369, 'reachGoal', 'opensttt');"><img class="img-fluid" src="<?= $last_material['image'] ? $last_material['image']->iconLink('-thumb') : '' ?>"></a>
                                            <span class="article__tag"><?= $last_material['rubric'] ?></span>
                                            
                                        </div>
                                    </div>
                                    <div class="tp-mt-materials-article-textbox tp-mt-order-2">
                                        <a class="article__heading" href="<?= $last_material['link'] ?>" onclick="ym(1383369, 'reachGoal', 'opensttt');"><?= $last_material['name'] ?></a>
                                        <p class="article__text"><?= $last_material['short_text'] ?></p>
                                        <div class="article__info">Источник: <?= $last_material['material_source_name'] ?>
                                            
                                            <?    
                                            if (true){?>
                                                <span>
                                                    <img src="/img3/eye.png"/>
                                                <?
                                                    echo ' '.$last_material['stat_see'].' ';
                                                ?>
                                                </span>
                                                <?
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
<? } ?>
                    </div>
                    <div class="recommends module">
                        <div class="block-title">
                            <div class="block-title__decore"></div>
                            <h2 class="block-title__heading">рекомендуем</h2>
                        </div>
                        <div class="reccomends-list module">
                            <div class="mdc-layout-grid__inner">
                                <?
                                $i = -1;
                                shuffle($recomend_materials);
                                foreach ($recomend_materials as $recomend_material) {
                                    $i++;
                                    if ($i == 4)
                                        break;
                                    ?>
                                    <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
                                        <article class="article article-card">
                                            <div class="article__img">
                                                <a class="article__heading article-card__heading" href="<?= $recomend_material['link'] ?>">
                                                    <img class="img-fluid" src="<?= $recomend_material['image'] ? $recomend_material['image']->iconLink('-thumb') : '' ?>">
                                                </a>
                                                <span class="article__tag"><?= $recomend_material['rubric'] ?></span></div><a class="article__heading article-card__heading" href="<?= $recomend_material['link'] ?>"><?= $recomend_material['short_text'] ?></a>
                                            <div class="article__info">Источник: <?= $recomend_material['material_source_name'] ?></div>
                                        </article>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </div> 

                    <div class="subscribe module">
                        <div class="mdc-layout-grid__inner">
                            <div class="mdc-layout-grid__cell--span-12-desktop mdc-layout-grid__cell--span-12-tablet mdc-layout-grid__cell--span-12-phone">
                                <a class="subscribe-card" href="mailto:delo@tovaryplus.ru" style="background-image: radial-gradient(50% 50% at 50% 50%,rgba(0,0,0,.2) 0,rgba(0,0,0,.7) 100%),url(/img3/hero-main.jpg);">
                                    <div class="subscribe-card__content">
                                        <h2 class="block-title__heading">Хотите разместить свою публикацаю?</h2>
                                        <p>Разместим ваш материал на одной из крупнейших площадок города - tovaryplus.ru</p>
                                    </div>
                                    <div class="article__tag">Написать нам: delo@tovaryplus.ru</div>
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="popular module">
                        <div class="block-title">
                            <div class="block-title__decore block-title__decore_light"></div>
                            <h2 class="block-title__heading">Популярное</h2>
                        </div>
                        <div class="popular-list">
<? foreach ($popular_materials as $popular_material) { ?>
                                <article class="article article_popular">
                                    <div class="mdc-layout-grid__inner">
                                        <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-1 mdc-layout-grid__cell--order-md-2"><a class="article__heading article__heading_light" href="<?= $popular_material['link'] ?>"><?= $popular_material['name'] ?></a>

                                            <div class="article__info article__info_end"><?= $popular_material['timestamp_last_updating'] ?></div>

                                        </div>
                                        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-2 mdc-layout-grid__cell--order-md-1">
                                            <div class="article__img"><img class="img-fluid" src="<?= $popular_material['image'] ? $popular_material['image']->iconLink('-thumb') : '' ?>"></div>
                                        </div>
                                    </div>
                                </article>
<? } ?>
                        </div>
                    </div>
<? if (false == true) { ?>
                        <div class="recommends module">
                            <div class="block-title">
                                <div class="block-title__decore"></div>
                                <h2 class="block-title__heading">рекомендуем</h2>
                            </div>
                            <div class="reccomends-list module">
                                <div class="mdc-layout-grid__inner">
    <?
    $i = -1;
    foreach ($recomend_materials as $recomend_material) {
        $i++;
        if ($i < 4)
            continue;
        ?>
                                        <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
                                            <article class="article article-card">
                                                <div class="article__img">
                                                    <a class="article__heading article-card__heading" href="<?= $recomend_material['link'] ?>">
                                                        <img class="img-fluid" src="<?= $recomend_material['image'] ? $recomend_material['image']->iconLink('-thumb') : '' ?>">
                                                    </a>
                                                    <span class="article__tag"><?= $recomend_material['rubric'] ?></span></div>
                                                <a class="article__heading article-card__heading" href="<?= $recomend_material['link'] ?>"><?= $recomend_material['name'] ?></a>
                                                <div class="article__info">Источник: <?= $recomend_material['material_source_name'] ?></div>
                                            </article>
                                        </div>
                        <? } ?>
                                </div>
                            </div>
                        </div>
                        <div class="btn-container">
                            <button class="btn btn_load-more"><img src="/img/reload.png"><span>Показать еще</span></button>
                        </div>
                    <? } ?>
                </div>

<? /* =============================================================================== */ ?>

                <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                    <?
                    $tg_post = ['461','460','512','506'];
                    shuffle($tg_post);?>
                    <style>
                        .for-tg>iframe{
                            
                            min-width: 160px!important;
                            max-width:320px;
                        }
                    </style>
                    <div class="for-tg">
                       <script async src="https://telegram.org/js/telegram-widget.js?14" data-telegram-post="tovaryplus/<?=$tg_post[0];?>" data-color="E22F38" data-dark-color="F95C54"></script> 
                    </div>
                    
                    
                    
                    <?= app()->chunk()->render('adv.index_right'); ?>
                    
                </div>
            </div>
        </div>
    </div>
</main>

<?
if (isset($position['bottom'])) {
    echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
}
?>

<div class="modal_box" id="modalidea" style="display:none">

</div>


