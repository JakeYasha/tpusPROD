<div class="brand-list">
    <?$i=0;$count=count($items);foreach ($items as $id => $item) {$i++;?>
		<div class="brand-list__item">
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                    <div class="image-wrapper<?if(!$item['image']){?> no-image<?}?>">
                        <a rel="nofollow" href="<?=$item['link']?>"><?if($item['image']){?><img class="img-fluid" src="<?=$item['image_thumb']?>" alt="<?=encode($item['name'])?>" /><?} else {?><img class="no-image img-fluid" src="/css/img/firm_logo.png"/><?}?></a>
                    </div>
                </div>
                <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone">
                    <h3 class="brand-list__item--heading">
                        <a href="<?=$item['link']?>" class="element-name" style="text-decoration: none;color: #34404e;"><?=$item['name']?></a>
                    </h3>
                    <?if ((isset($item['price']) && $item['price']) || (isset($item['price_wholesale']) && $item['price_wholesale'])) {?>
                        <div class="product-price">
                            <?if($item['old_price'] !== null){?>
                            <span class="common-price have-discount"><?=$item['price']?> <?=$item['currency']?></span>
                            <span class="old-price"><?=$item['old_price']?> <?=$item['currency']?></span>
                            <?} else {?>
                            <span class="common-price"><?=$item['price']?> <?=$item['currency']?></span>
                            <?}?>
                            <?if($item['unit']){?><span class="unit-type">цена за <?=$item['unit']?></span><?}?>
                            <?if($item['price_wholesale'] && $item['price_wholesale'] !== $item['price']){?>
                            <span class="unit-type">опт: <?=$item['price_wholesale']?> <?= $item['currency']?></span>
                            <?}?>
                        </div>
                    <?}?>
                    <p class="brand-list__item--text">
                        <?=$item['is_yml'] ? $item['description_short_away'] : $item['description_short']?>
                    </p>
                    <? if(str()->length($item['vendor']) > 2) {?>
                        <div class="brand-list__item--service">
                            <div class="brand-list__item--info">Производитель:</div>
                            <div class="brand-list__item--content"><?=$item['vendor']?></div>
                        </div>
                    <?} elseif($item['production']){?>
                        <div class="brand-list__item--service">
                            <div class="brand-list__item--info">Производство:</div>
                            <div class="brand-list__item--content"><?=$item['production']?></div>
                        </div>
                    <?}?>
                    <?/*=app()->chunk()->set('firm', $item['firm'])->set('item', $item)->set('id', $id)->render('common.button_set_price_small')*/?>
                </div>
            </div>
			<div class="divider"></div>
		</div>
    <?}?>
</div>