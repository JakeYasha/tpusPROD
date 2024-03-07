<?= $bread_crumbs ?>
<?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
?>
<div class="cat_description">
    <h1><?= app()->metadata()->getHeader() ?></h1>
</div>
<div class="promo-rubrics">
    <ul class="promo-rubrics-list clearfix">
        <li class="promo-rubrics-list-item <?=$path == '' ? 'active' : '' ?>"><a href="<?=($_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] 
     . explode('?', $_SERVER['REQUEST_URI'], 2)[0])?>"><i class="spr-rub spr-rub-all"></i>Все</a></li>
        <?foreach($promo_rubrics as $promo_rubric_name => $promo_rubric) {?>
            <li class="promo-rubrics-list-item <?=$path == md5($promo_rubric_name) ? 'active' : '' ?>"><a href="?path=<?=md5($promo_rubric_name)?>"><i class="spr-rub spr-rub-<?=$promo_rubric['sprite']?>"></i><?=$promo_rubric_name?></a></li>
        <?}?>
    </ul>
</div>

<div class="item_info">
    <?/*= app()->chunk()->set('filter', $filters)->set('items', $tags)->set('link', app()->link('/advert-module/'))->render('common.catalog_am_tags') */?>
    <div class="hp_coupon_block search_result">
        <?= $items ?>
    </div>
    <div class="cat_description no-margin">
        <div class="hp_clear"></div>
        <?= $promo ?>
        <div class="hp_clear"></div>
    </div>
</div>
<div class="item_info">
    <div class="cat_description notice-dark-grey popular-yml-firms small">
        <h2>Популярные магазины</h2>
        <div class="jcarousel-wrapper">
            <div class="pop_item_slider jcarousel js-jcarousel">
                <ul>
                    <?
                    $i = 0;
                    foreach ($yml_firms as $item) {
                        $i++;
                        ?>
                        <li class="leaf">
                            <div class="image">
                                <a href="/page/away/firm/<?= $item->id()?>/"><img<? if (!$item->hasLogo()) {?> class="no-image"<? }?> src="<?= $item->logoPath()?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;'])?>, <?= str()->replace($item->address(), ['"'], ['&quot;'])?>"></a>
                            </div>
                        </li>
                    <? } ?>
                </ul>
            </div>
            <a href="#" class="jcarousel-control-prev"></a>
            <a href="#" class="jcarousel-control-next"></a>
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
<div class="pre_footer_adv_block">
	<?=app()->chunk()->render('adv.bottom_banners')?>
</div>	

<?= $advert_restrictions ?>
<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
?>
