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
<div class="search-result-element-block firm-wrapper">
	<div class="element-image-block">
		<div class="image-wrapper">
			<? if ($item->hasWebPartner()) {?>
				<a rel="nofollow" target="_blank" href="/page/away/firm/<?= $item->id().'/'?>"><img<? if (!$item->hasLogo()) {?> class="no-image"<? }?> src="<?= $item->logoPath()?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;'])?>, <?= str()->replace($item->address(), ['"'], ['&quot;'])?>"></a>
			<? } else {?>
				<a href="<?= $item->link()?>"><img<? if (!$item->hasLogo()) {?> class="no-image"<? }?> src="<?= $item->logoPath()?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;'])?>, <?= str()->replace($item->address(), ['"'], ['&quot;'])?>"></a>
			<? }?>
		</div>
	</div>
	<div class="element-info-block for-firm">
		<? if ($item->hasWebPartner()) {?>
			<a rel="nofollow" target="_blank" href="/page/away/firm/<?= $item->id().'/'?>" class="element-name<? if ($item->isBlocked()) {?> gray<? }?>"><?= $item->name()?></a>		
		<? } else {?>
			<a href="<?= $item->link()?>" class="element-name<? if ($item->isBlocked()) {?> gray<? }?>"><?= $item->name()?></a>
		<? }?>			
		<?/* if (!isset($show_rating) || $show_rating) {?><div class="top_review"><?= app()->chunk()->setArg($item)->render('rating.stars')?></div><? }*/?>
        <?= app()->chunk()->setArg($item)->render('rating.only_button')?>
		<? if ($item->hasActivity()) {?><div class="element-description<?if($presenter instanceof \App\Presenter\FirmItems && $presenter->getForceHideActivity()){?> force-hide<?}?>"><p><?= $item->activity()?></p></div><? }?>
		<a href="#" class="js-show-contacts btn-base btn-grey show-contacts" data-firm-id="<?=$item->id()?>"><span class="show-contacts-text">Показать контакты</span></a>
		<div class="firm-contacts real-contacts js-show-contacts-wrapper">
			<? if ($item->hasPhone()) {?>
				<div class="firm-contacts-line">
					<span class="contact-type">Телефон:</span>
					<span><?= $item->renderPhoneLinks()?></span>
				</div>
			<? }?>
			<div class="firm-contacts-line">
				<span class="contact-type">Адрес:</span>
				<span><?= $item->address()?></span>
			</div>
			<? if ($item->hasModeWork()) {?>
				<div class="firm-contacts-line">
					<span class="contact-type">Режим работы:</span>
					<span><?= $item->modeWork()?></span>
				</div>
			<? }?>
			<? if ($item->hasWeb()) {?>
				<div class="firm-contacts-line site-address">
					<span class="contact-type">Сайт:</span>
					<span><?= $item->renderWebLinks()?></span>
				</div>
			<? }?>
            <?if (isset($special_price_links) && isset($special_price_links[$item->id()])) {?>
                <? $link = $special_price_links[$item->id()][0]; ?>
                <div class="firm-contacts-line">
                    <span class="contact-type">Предложения:</span>
                    <span><a href="<?=$item->link() . $link['url']?>" rel="nofollow"><?=$link['name']?></a></span>
                </div>
            <?}?>
            <? $firm_branches_count = $item->getFirmBranchesCount();
            if($firm_branches_count){ ?>
                <div class="firm-contacts-line">
                    <span class="contact-type more_addresses"><a href="<?= ($item->hasWebPartner() ? '/page/away/firm/' . $item->id() . '/' : $item->link())?>" rel="nofollow" target="_blank">еще <?=$firm_branches_count?> <?=  \CWord::ending($firm_branches_count, ['адрес','адреса','адресов'])?></a></span>
                </div>
            <?}?>
		</div>
		<? if (isset($catalogs_count[$item->id()])) {?>
			<div class="firm-contacts">
				<div class="firm-contacts-line firm-contacts-line-sections">
					<span class="contact-type">Предложения:</span>
					<span>
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
					</span>
				</div>
			</div>
		<? }?>

		<? if (!isset($show_buttons) || $show_buttons) {?>
			<div class="buttons-block">
                <? if (!isset($catalogs_count[$item->id()])) {?>
                    <? if ($item->hasPriceList()) {?><a class="btn-base btn-red" href="<?= $item->hasWebPartner() ? ('/page/away/firm/' . $item->id() . '/') : $item->linkPricelist(/*(isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null*/)?>" rel="nofollow">Прайс-лист</a><? } else {?><a class="btn-base btn-grey disabled" href="#" onclick="return false;" rel="nofollow">Прайс-лист</a><? }?>
				<? } else {?>
					<a class="btn-base btn-red" rel="nofollow" href="<?= $item->hasWebPartner() ? ('/page/away/firm/' . $item->id() . '/') : $item->linkPricelist((isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null)?>"><span>Показать предложения</span></a>
				<?}?>
				<? if ($item->hasEmail()) {?><a class="btn-base btn-grey fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/<?= $item->id()?>/" rel="nofollow">Оставить сообщение</a><? } else {?><a class="btn-base btn-grey disabled" href="#" onclick="return false;" rel="nofollow">Оставить сообщение</a><? }?>
				<? if ($item->hasCellPhone() && $item->id_service() == 10) {?><a class="btn-base btn-grey btn-show-more fancybox fancybox.ajax" href="/firm-feedback/get-callback-form/<?= $item->id()?>/" rel="nofollow">Заказать звонок</a><? } else /*elseif ($item->id_service() == 10)*/ {?><a class="btn-base btn-grey btn-show-more disabled" href="#" onclick="return false;" rel="nofollow">Заказать звонок</a><? }?>
                <? if ($item->hasWebPartner()) {?>
					<a rel="nofollow" title="Перейти на сайт магазина" target="_blank" href="/page/away/firm/<?= $item->id().'/'?>" class="btn-base btn-grey to-firm js-click-url turbo" data-object-id="<?=$item->id()?>"></a>
				<? } else if ($item->hasPhone()) {?>
					<a rel="nofollow" href="#firm-call-form-<?= $item->id()?>" class="btn-base btn-grey to-firm call fancybox" data-object-id="<?=$item->id()?>"></a>
				<? } else {?>
					<a title="Подробнее о фирме" href="<?= $item->link()?>" class="btn-base btn-grey to-firm js-click-url" data-object-id="<?=$item->id()?>"></a>
					<?/*<a title="Подробнее о предложении" rel="nofollow" href="<?= $item->linkPricelist((isset($active_catalog) && $active_catalog) ? $active_catalog->id() : null)?>" class="btn-base btn-grey to-firm"></a>*/?>
				<? }?>
                <?= app()->chunk()->setVar('heading', 'Телефоны')->setVar('firm', $item)->render('forms.firm_call_static_form'); ?>
			</div>
		<? }?>
	</div>
</div>