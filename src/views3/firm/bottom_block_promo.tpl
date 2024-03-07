<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell--span-12">
		<?= $tabs?>
		<? if($items) { ?>
            <h2><?=app()->metadata()->getHeader()?></h2>
            <div class="special module">
                <div class="mdc-layout-grid__inner">
                    <?$i=0;foreach ($items as $item) {$i++;
                                $id_firm = $item['id_firm'];
                                $id_service = $item['id_service'];
                                $text = $item['text'];
                                $text = str()->replace(str()->replace($text, ' target="_blank"', ''), ' rel="nofollow"', '');
                                $text = preg_replace_callback('~href="([^"]+)"~u', function($matches) use ($id_firm, $id_service) {
                                        return 'target="_blank" rel="nofollow" href="' . app()->away(trim($matches[1]), $id_firm) . '"';
                                }, $text);
                                ?>
                        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
                            <article class="article article-card">
                                <div class="article__img">
                                    <a href="<?=$item['link']?>">
                                        <?if($item['image']){?><img class="img-fluid" src="<?=$item['image']?>" alt="<?=str()->replace($item['name'], ['"'], ['&quot;'])?>"><?}?>
                                    </a>
                                    <span class="article__tag">
                                        <?if($item['flag_is_present']){?>
                                            <div class="promo-value present"></div>
                                        <?} elseif($item['percent_value']) {?>
                                            <div class="promo-value"><?=$item['percent_value']?>%</div>
                                        <?} else {?>
                                            <div class="promo-value">SALE</div>
                                        <?}?>
                                    </span>
                                </div>
                                <div class="article__info article__info_special">
                                    <?=$item['flag_is_infinite'] ? '<span class="promo-infinite">Постоянная акция</span>' : 'с '.$item['time_beginning'].' по '.$item['time_ending']?>
                                </div>
                                <a class="article__heading article-card__heading article__heading_special" href="<?=$item['link']?>"><?=$item['name']?></a>
                                        <!-- aasaaaaaa  -->
                            </article>
                        </div>
                    <?}?>
                </div>
            </div>
		<?}?>
	</div>
</div>
<br/>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>