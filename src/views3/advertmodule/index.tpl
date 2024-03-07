<!--adviertmodule index-->
<style>
    .article-card>.article__img>a:not([class])>.img-fluid{
        max-height: 168px;
        object-fit: contain;
    }
</style>

<div class="mdc-layout-grid">
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <?= $bread_crumbs ?>
    <div class="block-title module">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading"><?= app()->metadata()->getHeader() ?></h1>
    </div>
    <div class="divider"></div>
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell">
            <a href="<?=($_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0])?>" class="service-item <?=$path == '' ? 'active' : '' ?>">Все</a>
        </div>
        <?foreach($promo_rubrics as $promo_rubric_name => $promo_rubric) {?>
            <div class="mdc-layout-grid__cell">
                <a href="?path=<?=md5($promo_rubric_name)?>" class="service-item <?=$path == md5($promo_rubric_name) ? 'active' : '' ?>"><?=$promo_rubric_name?></a>
            </div>
        <?}?>
    </div>
    <div class="divider"></div>
    <div class="special module">
        <div class="mdc-layout-grid__inner">
            <?= $items ?>
        </div>
        <div class="divider"></div>
        <div class="mdc-layout-grid__inner">
            <?= $promo ?>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-12">
            <h2>Популярные магазины</h2>
            <div class="jcarousel-wrapper w320">
                <div class="jcarousel js-jcarousel">
                    <ul>
                        <?$i = 0;foreach ($yml_firms as $item) {$i++;?>
                            <li>
                                <a href="/page/away/firm/<?= $item->id()?>/">
                                    <img<? if (!$item->hasLogo()) {?> class="no-image"<? }?> src="<?= $item->logoPath()?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;'])?>, <?= str()->replace($item->address(), ['"'], ['&quot;'])?>">
                                </a>
                            </li>
                        <? } ?>
                    </ul>
                </div>
                <a class="jcarousel-control-prev"></a>
                <a class="jcarousel-control-next"></a>
            </div>
        </div>
    </div>
    
    <div class="cat_description">
        <div class="hp_clear"></div>
        <?= $text ?>
        <div style="color: #7a7a7a; margin: 25px 0;">
            <p>
                Информацию об организаторах акций, о правилах их проведения, количестве призов и подарков, сроках, месте и порядке их получения можно получить по телефонам и адресам, указанным в рекламных модулях рекламодателей.
            </p>
        </div>
        <div class="hp_clear"></div>
        <div class="notice-dark-grey">
            <h2 style="text-align: center">Хотите разместить здесь свои предложения и акции?</h2>
            <p style="text-align: center;">Создайте заявку на размещение информации на сайте tovaryplus.ru и наш менеджер свяжется с вами для уточнения деталей заявки. </p>
            <div style="text-align: center;"><a class="button_red" href="/request/add/"> Создать заявку </a></div>
        </div>
        <div class="for_clients_text_c advert_wrapper clearfix page">
        </div>
    </div>
    <br/>
    <div class="pre_footer_adv_block">
        <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>	

    <?= $advert_restrictions ?>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>