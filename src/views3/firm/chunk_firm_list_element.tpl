<?
if (!$item->isForCurrentCity()) {
    $firm_branches = $item->getCityFirmBranches();
    if (count($firm_branches) > 0) {
        $firm_branch = current($firm_branches);
        $vals = $firm_branch->getVals();
        unset($vals['id']);
        $item->setVals($vals);
        $item->branch_id = $firm_branch->id();
        $item->flag_is_price_attached = $firm_branch->val('flag_is_price_attached');
    }
}
?>
<div class="brand-list__item">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
            <? if ($item->hasWebPartner()) {?>
                <a rel="nofollow" target="_blank" href="/page/away/firm/<?= $item->id().'/'?>"><img<? if (!$item->hasLogo()) {?> class="no-image img-fluid"<? } else {?> class="img-fluid"<?}?> src="<?= $item->logoPath()?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;'])?>, <?= str()->replace($item->address(), ['"'], ['&quot;'])?>"></a>
            <? } else {?>
                <a href="<?= $item->link()?>"><img<? if (!$item->hasLogo()) {?> class="no-image img-fluid"<? } else {?> class="img-fluid"<?}?> src="<?= $item->logoPath()?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;'])?>, <?= str()->replace($item->address(), ['"'], ['&quot;'])?>"></a>
            <? }?>
        </div>
        <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone">
            <? if ($item->hasWebPartner()) {?>
                <h3 class="brand-list__item--heading"><a style="text-decoration: none;color: #34404e;" rel="nofollow" target="_blank" href="/page/away/firm/<?= $item->id().'/'?>" class="element-name<? if ($item->isBlocked()) {?> gray<? }?>"><?= $item->name()?></a></h3>
            <? } else {?>
                <h3 class="brand-list__item--heading"><a style="text-decoration: none;color: #34404e;" href="<?= $item->link()?>" class="element-name<? if ($item->isBlocked()) {?> gray<? }?>"><?= $item->name()?></a></h3>
            <? }?>			
            <?/* if (!isset($show_rating) || $show_rating) {?><div class="top_review"><?= app()->chunk()->setArg($item)->render('rating.stars')?></div><? }*/?>
            <?/*= app()->chunk()->setArg($item)->render('rating.only_button')*/?>
            <? if ($item->hasActivity()) {?>
                <p class="brand-list__item--text <?if($presenter instanceof \App\Presenter\FirmItems && $presenter->getForceHideActivity()){?> force-hide<?}?>"><?= $item->activity()?></p>
            <? }?>
            <?/*<a href="#" class="js-show-contacts btn-base btn-grey show-contacts" data-firm-id="<?=$item->id()?>"><span class="show-contacts-text">Показать контакты</span></a>*/?>

            <? if ($item->hasPhone()) {?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Телефон:</div>
                    <div class="brand-list__item--content">
                        <?= $item->renderPhoneLinks()?>
                    </div>
                </div>
            <? }?>
            <div class="brand-list__item--service">
                <div class="brand-list__item--info">Адрес:</div>
                <div class="brand-list__item--content"><?= $item->address()?></div>
            </div>
            <? if ($item->hasModeWork()) {?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Режим работы:</div>
                    <div class="brand-list__item--content"><?= $item->modeWork()?></div>
                </div>
            <? }?>
            <? if ($item->hasWeb()) {?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Сайт:</div>
                    <div class="brand-list__item--content"><?= $item->renderWebLinks()?></div>
                </div>
            <? }?>
            <?if (isset($special_price_links) && isset($special_price_links[$item->id()])) {?>
                <? $link = $special_price_links[$item->id()][0]; ?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Предложения:</div>
                    <div class="brand-list__item--content brand-list__item--text"><a href="<?=$item->link() . $link['url']?>" rel="nofollow"><?=$link['name']?></a></div>
                </div>
            <?}?>
            <? $firm_branches_count = $item->getFirmBranchesCount();
            if($firm_branches_count){ ?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Филиалы:</div>
                    <div class="brand-list__item--content brand-list__item--text"><a href="<?= ($item->hasWebPartner() ? '/page/away/firm/' . $item->id() . '/' : $item->link())?>" rel="nofollow" target="_blank"><?=$firm_branches_count?> <?=  \CWord::ending($firm_branches_count, ['адрес','адреса','адресов'])?></a></div>
                </div>
            <?}?>

            <? if (isset($catalogs_count[$item->id()])) {?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Предложения:</div>
                    <div class="brand-list__item--content brand-list__item--text">
                        <?
                        $i = 0;
                        $_max_childs = 12;
                        $ccount = count($catalogs_count[$item->id()]);
                        foreach ($catalogs_count[$item->id()] as $cat_id => $count) {
                            if (!isset($catalogs[$cat_id])){$ccount--;continue;}$i++;
                            if ($i > $_max_childs) break;
                            ?>
                            <? if ($i == 1) {?>
                                <a rel="nofollow" href="<?= $catalogs[$cat_id]->linkPriceList($item)?>"><?= $catalogs[$cat_id]->name()?></a>&nbsp;<span>(<?= $catalogs_count[$item->id()][$cat_id]?>)</span><?= $ccount >= 1 ? ': ' : ''?>
                            <? } else {?>
                                <span><?= $catalogs[$cat_id]->val('web_name')?>; <?= $i == $_max_childs && $count > $_max_childs ? '...' : ''?></span>
                            <? }?>
                        <? }?>
                    </div>
                </div>            
            <? }?>
            
            <? if (!isset($show_buttons) || $show_buttons) {?>
                <div class="brand-list__actions brand-list__actions_desktop">
                    <? if (!isset($catalogs_count[$item->id()])) {?>
                        <? if ($item->hasPriceList()) {?>
                            <a rel="nofollow" class="btn brand-list__action btn_primary" href="<?= $item->hasWebPartner() ? ('/page/away/firm/' . $item->id() . '/') : $item->linkPricelist(/*(isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null*/)?>">показать предложения</a>
                        <? } else {?>
                            <a rel="nofollow" class="disabled btn brand-list__action btn_outline btn_outline--secondary" onclick="return false;" href="#">показать предложения</a>
                        <?}?>
                    <? } else {?>
                        <a rel="nofollow" class="btn brand-list__action btn_primary" href="<?= $item->hasWebPartner() ? ('/page/away/firm/' . $item->id() . '/') : $item->linkPricelist((isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null)?>">показать предложения</a>
                    <?}?>
                    <? if ($item->hasEmail()) {?>
                        <a rel="nofollow" class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="feedbackForm" data-url="/firm-feedback/get-feedback-form/<?= $item->id()?>/">Отправить сообщение</a>
                    <? } else {?>
                        <a rel="nofollow" class="disabled btn brand-list__action btn_outline btn_outline--secondary" onclick="return false;" href="#">Отправить сообщение</a>
                    <?}?>
                    <? if ($item->hasCellPhone() && $item->id_service() == 10) {?>
                        <a rel="nofollow" class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="callbackForm" data-url="/firm-feedback/get-callback-form/<?= $item->id()?>/">заказать звонок</a>
                    <? } else {?>
                        <a rel="nofollow" class="disabled btn brand-list__action btn_outline btn_outline--secondary" onclick="return false;" href="#">заказать звонок</a>
                    <?}?>
                    <!--a rel="nofollow" class="btn brand-list__action btn_outline btn_icon btn_outline--primary" href=""><span class="phone-icon"></span></a-->
                </div>
                <div class="brand-list__actions brand-list__actions_mobile">
                    <? if (!isset($catalogs_count[$item->id()])) {?>
                        <? if ($item->hasPriceList()) {?>
                            <a rel="nofollow" class="btn brand-list__action btn_primary" href="<?= $item->hasWebPartner() ? ('/page/away/firm/' . $item->id() . '/') : $item->linkPricelist(/*(isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null*/)?>">показать предложения</a>
                        <? } else {?>
                            <a rel="nofollow" class="disabled btn brand-list__action btn_outline btn_outline--secondary" onclick="return false;" href="#">показать предложения</a>
                        <?}?>
                    <? } else {?>
                        <a rel="nofollow" class="btn brand-list__action btn_primary" href="<?= $item->hasWebPartner() ? ('/page/away/firm/' . $item->id() . '/') : $item->linkPricelist((isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null)?>">показать предложения</a>
                    <?}?>
                    <? if ($item->hasEmail()) {?>
                        <a rel="nofollow" class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="feedbackForm" data-url="/firm-feedback/get-feedback-form/<?= $item->id()?>/">Отправить сообщение</a>
                    <? } else {?>
                        <a rel="nofollow" class="disabled btn brand-list__action btn_outline btn_outline--secondary" onclick="return false;" href="#">Отправить сообщение</a>
                    <?}?>
                    <? if ($item->hasPhone()) {?>
                        <a rel="nofollow" class="btn brand-list__action btn_outline btn_outline--primary js-open-modal" data-target="callForm<?= $item->id()?>">Позвонить</a>
                    <? } else {?>
                        <a rel="nofollow" class="disabled btn brand-list__action btn_outline btn_outline--secondary" onclick="return false;" href="#">Позвонить</a>
                    <?}?>
                    <?= app()->chunk()->setVar('heading', 'Телефоны')->setVar('firm', $item)->render('forms.firm_call_static_form'); ?>
                </div>
            <? }?>
        </div>
    </div>
	<div class="divider"></div>
</div>