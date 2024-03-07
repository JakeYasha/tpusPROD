<div class="mdc-layout-grid">
    <?= $bread_crumbs?>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <div class="for_clients clearfix">
        <div class="for_clients_text_c clearfix page">
                <h1><?=$item->name()?></h1>
                <br/>
                <h2>Представительство TovaryPlus.ru для региона <?=$region?></h2>
                <?=$item->val('info')?>
                <h2>Контакты:</h2>
                <? if ($item->val('address')) {?>
                        <p><strong>Адрес</strong>: <?=$item->val('address')?></p>
                <?}?>
                <? if ($item->val('phone')) {?>
                <p><strong>Телефон</strong>: <?=$item->val('phone')?></p>
                <?}?>
                <? if ($item->hasWeb()) {?>
                        <p><strong>Веб-сайт</strong>: 
                            <?foreach ($item->getWeb() as $url) {?>
                                    <a target="_blank" href="<?=app()->away(trim($url))?>" rel="nofollow"><?=trim($url)?></a>,
                            <?}?>
                            <?if (app()->location()->currentId() != '76004') {?><a href="/<?=$item->val('id_city')?>">www.tovaryplus.ru/<?=$item->val('id_city')?></a>, <?}?>www.ratiss.org</p>
                <?}?>
                <? if ($item->val('email')) {?>
                        <p><strong>E-mail</strong>: <a href="mailto:<?=$item->val('email')?>"><?=$item->val('email')?></a></p>
                <?}?>
                <? if ($item->val('mode_work')) {?>
                        <p><strong>Режим работы</strong>: <?=$item->val('mode_work')?></p>
                <?}?>
                <div class="js-map-points hidden">
                        <?if($item->hasAddress()){?>
                        <div class="js-map-points-coord hidden" data-id="<?=$item->id()?>" data-name="<?=  htmlspecialchars($item->name())?>" <? if ($coords) {?> data-coords-lat="<?= $coords['lat']?>" data-coords-id="<?= $coords['id']?>" data-coords-lng="<?= $coords['lng']?>"<? } else {?> data-address="<?= $item->val('address')?>"<? }?>>
                                <div class="popup_in_map">
                                        <div class="manuf_field">
                                                <div class="man_desc">
                                                <div class="name_firm_head"><?=$item->name()?></div>
                                                <?if ($item->hasAddress()) {?>
                                                        <b>Адрес:</b> <?=$item->val('address')?><br/>
                                                <?}?>
                                                <?if ($item->val('phone')) {?>
                                                        <b>Телефон:</b> <?=$item->val('phone')?><br/>
                                                <?}?>
                                                <?if ($item->val('mode_work')) {?>
                                                        <b>Режим работы:</b> <?=$item->val('mode_work')?><br/>
                                                <?}?>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <?}?>
                </div>
                <?if($item->hasAddress()){?>
                        <div class="map_field_service">
                                <h2>Карта проезда:</h2>
                                <div id="map"></div>
                        </div>
                <?}?>
        </div>
    </div>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>