<? if ($items && current($items)->id() != 0) { ?>
    <div class="bottom_alert" style="display: block;">
        <? $i = 0; foreach ($items as $item) { $i++; ?>
            <img<? if ($i > 1) { ?> style="display: none;"<? } ?> class="js-advert-restriction-img<? if ($i === 1) { ?> js-advert-restriction-img-active<? } ?>" alt="<?= $item->name() ?>" src="<?= $item->image() ?>">
        <? } ?>
    </div>
<? } ?>