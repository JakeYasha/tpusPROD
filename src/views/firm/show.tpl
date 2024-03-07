<? /* @var $item Firm */ ?>
<? /* @var $branches Firm[] */ ?>
<?= $bread_crumbs ?>
<? if (!$item->isBlocked()) { ?>
    <div class="firm_field<? if ($mode) { ?> mobile_hidden<? } ?>">
        <div class="image_field">
            <div class="image"><span><img src="<?= $item->logoPath() ?>" alt="<?= encode($item->name()) ?>, <?= encode($item->address()) ?>" title="<?= encode($item->name()) ?>, <?= encode($item->address()) ?>"></span></div>
        </div>
        <div class="description">
            <h1><?= $item->name() ?></h1>
            <?= app()->chunk()->setArgs([$item->val('rating'), false, $item])->render('rating.stars') ?>
            <? if ($item->hasActivity()) { ?><p><?= $item->activity() ?></p><? } ?>
            <div class="contacts">
                <div class="title">контакты:</div>
                <? if ($item->hasPhone()) { ?><p><span class="l">Телефон:</span><span class="r tel"><?= $item->phone() ?></span></p><? } ?>
                <? if ($item->hasFax()) { ?><p><span class="l">Факс:</span><span class="r tel"><?= $item->fax() ?></span></p><? } ?>
                <p><span class="l">Адрес:</span><span class="r"><?= $item->address() ?><? if (count($branches)) { ?>&nbsp;<a rel="nofollow" href="<?= $item->link() ?>#map" class="branch">(еще <?= count($branches) ?> <?= \CWord::ending(count($branches), ['филиал', 'филиала', 'филиалов']) ?>)</a><? } ?></span></p>
                <? if ($item->hasModeWork()) { ?><p><span class="l">Режим работы:</span><span class="r"><?= $item->modeWork() ?></span></p><? } ?>
                <? if ($item->hasEmail()) { ?>
                    <p><span class="l">Email:</span><span class="r"><a class="fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/<?= $item->id_firm() ?>/<?= $item->id_service() ?>/" rel="nofollow"><?= $item->firstemail() ?></a></span></p>
                <? } ?>
                <? if ($item->hasWeb()) { ?>
                    <p><span class="l">Сайт:</span><span class="r"><?
                            $i = 0;
                            $_i = count($item->webSiteUrls());
                            if ($item->hasWebPartner()) {
                                foreach ($item->webSiteUrls() as $url) {
                                    $i++;
                                    ?><a target="_blank" href="/page/away/firm/<?= $item->id() ?>/" rel="nofollow" ><?= trim($url) ?></a><? if ($_i !== $i) { ?>, <? } ?> <?
                                }
                            } else {
                                foreach ($item->webSiteUrls() as $url) {
                                    $i++;
                                    ?>
                                    <a target="_blank" href="<?= app()->away($url, $item->id()) ?>" rel="nofollow" ><?= trim($url) ?></a><? if ($_i !== $i) { ?>, <? } ?> 
                                <? }
                            } ?>

                        </span>
                    </p>
				<? } ?>
            </div>
			<div class="contacts">
				<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
				<script src="//yastatic.net/share2/share.js"></script>
				<p><span class="l">Поделись информацией: </span></p>
				<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,viber,whatsapp,skype,telegram"></div>
			</div>	
		</div>
    </div>
    <a class="js-firm-bottom-block" name="firm-bottom-block"></a>
    <?= app()->chunk()->setArg($item)->render('adv.firm_top_banner') ?>
    <?= $bottom_block ?>
    <div class="search_result">
        <div class="error-info">
            Если вы увидели неточность в представленных данных по фирме <?= $item->name() ?>, <a class="fancybox fancybox.ajax" href="/firm-feedback/get-error-form/<?= $item->id_firm() ?>/<?= $item->id_service() ?>/" rel="nofollow">сообщите об ошибке</a>. Мы уточним информацию о фирме и внесем изменения в данные.
        </div>
    </div>
<? } else { ?>
    <div class="firm_field<? if ($mode) { ?> mobile_hidden<? } ?>">
        <div class="image_field">
            <div class="image"><span><img src="<?= $item->logoPath() ?>" alt="<?= $item->name() ?>, <?= $item->address() ?>" title="<?= $item->name() ?>, <?= $item->address() ?>"></span></div>
        </div>
        <div class="description">
            <h1 class="grey"><?= $item->name() ?></h1>
    <? if ($item->hasActivity()) { ?><p><?= $item->activity() ?></p><? } ?>
            <div class="contacts grey">
                <div class="title">контакты:</div>
                <? if ($item->hasPhone()) { ?><p><span class="l">Телефон:</span><span class="r tel"><?= $item->phone() ?></span></p><? } ?>
                <? if ($item->hasFax()) { ?><p><span class="l">Факс:</span><span class="r tel"><?= $item->fax() ?></span></p><? } ?>
                <p><span class="l">Адрес:</span><span class="r"><?= $item->address() ?></span></p>
    <? if ($item->hasModeWork()) { ?><p><span class="l">Режим работы:</span><span class="r"><?= $item->modeWork() ?></span></p><? } ?>
            </div>
        </div>
    </div>
    <a class="js-firm-bottom-block" name="firm-bottom-block"></a>
    <?= $bottom_block ?>
<? } ?>

<?=
app()->adv()->renderRestrictions()?>