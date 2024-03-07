<? if ($items) { ?>
    <!--h2>Спецпредложения, скидки и акции <?= app()->location()->currentName('prepositional') ?></h2-->
    <? foreach ($items as $k => $advert_module) { ?>
        <div class="mdc-layout-grid__cell"><!--123-->
            <article class="article article-card">
                <div class="article__img">
                    <? if ($advert_module->hasMoreUrl()) { ?>
                        <? if ($advert_module->hasFullImage()) { ?>
                            <a rel="nofollow" target="_blank" href="/advert-module/url/<?= $advert_module->id() ?>/" class="hp_company_site">
                                <img class="img-fluid" src="<?= $advert_module->getFullImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                            </a>
                        <? } else { ?>
                            <a rel="nofollow" target="_blank" href="/advert-module/url/<?= $advert_module->id() ?>/" class="hp_company_site">
                                <img class="img-fluid" src="<?= $advert_module->getImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                            </a>
                        <? } ?>
                    <? } else if ($advert_module->hasUrl()) { ?>
                        <? if ($advert_module->hasFullImage()) { ?>
                            <a rel="nofollow" target="_blank" href="/advert-module/item/<?= $advert_module->id() ?>/" class="hp_company_site">
                                <img class="img-fluid" src="<?= $advert_module->getFullImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                            </a>
                        <? } else { ?>
                            <a rel="nofollow" target="_blank" href="/advert-module/item/<?= $advert_module->id() ?>/" class="hp_company_site">
                                <img class="img-fluid" src="<?= $advert_module->getImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                            </a>
                        <? } ?>
                    <? } else { ?>
                        <? if ($advert_module->hasFullImage()) { ?>
                            <a rel="nofollow" href="#" onclick="return false;">
                                <img class="img-fluid" src="<?= $advert_module->getFullImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                            </a>
                        <? } else { ?>
                            <a rel="nofollow" href="#" onclick="return false;">
                                <img class="img-fluid" src="<?= $advert_module->getImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                            </a>
                        <? } ?>
                    <? } ?>
                </div>
                <div class="article__info article__info_special"><? if ($advert_module->val('flag_is_infinite')) { ?>Постоянная<? } else { ?>c <?= date("d.m.Y", CDateTime::toTimestamp($advert_module->val('timestamp_beginning'))) ?> по <?= date("d.m.Y", CDateTime::toTimestamp($advert_module->val('timestamp_ending'))) ?><? } ?></div>
                <a class="article__heading article-card__heading article__heading_special" href="/advert-module/item/<?=$advert_module->id()?>/"><?= $advert_module->val('header')?></a>
                        <!-- sadasdasd -->
            </article>
        </div>
    <? } ?>
<?}?>