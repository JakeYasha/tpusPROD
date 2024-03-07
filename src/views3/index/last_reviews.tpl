<? if ($items) { ?>
    <section class="slider">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-2-tablet mdc-layout-grid__cell--span-4-phone">
                <div class="block-title">
                    <div class="block-title__decore"></div>
                    <h2 class="block-title__heading">новые отзывы о компаниях</h2>
                </div>
            </div>
        </div>
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell--span-12">
                <div class="reviews-slider">
                    <? if (count($items) > 3) {?>
                        <div class="reviews-slider__controls">
                            <div class="reviews-slider__control reviews-slider__control_left"><img src="/img3/slider-arrow.png"></div>
                            <div class="reviews-slider__control reviews-slider__control_right"><img src="/img3/slider-arrow.png"></div>
                        </div>
                    <? } ?>
                    <div class="reviews-slider__content">
                        <? foreach ($items as $item) { ?>
                            <div class="reviews-slider__item">
                                <div class="reviews-slider__heading">
                                    <p class="reviews-slider__heading--name"><a href="<?= $item['firm']->link() ?>" class="company_name"><?= $item['firm']->name() ?></a></p>
                                    <div class="reviews-slider__heading--rating">
                                        <span class="reviews-slider__heading--stars">
                                            <? for($s = 0; $s < $item['score']; $s++) { ?>
                                                <img src="/img3/star.svg" alt="Звезда"/>
                                            <? } ?>
                                        </span>
                                        <span class="reviews-slider__heading--counter"><?= app()->chunk()->setArg($item['firm'])->render('rating.only_button')?></span>
                                    </div>
                                </div>
                                <div class="reviews-slider__body">
                                    <? $full_text = strip_tags($item['text']);
                                        if (str()->length($full_text) < 150) { ?>
                                        <div><?= $full_text ?></div>
                                        <? } else {
                                            $before_text = str()->crop($full_text, 100, '.');
                                            $after_text = str()->sub($full_text, (str()->length($before_text) - 2)); ?>
                                            <div><?= $before_text ?></div>
                                            <div style="display: none"><?= $after_text ?></div>
                                            <a class="reviews-slider__body--link" href="#">Читать далее</a><br/>
                                        <? } ?>
                                    <p class="reviews-slider__body--link"><?= $item['user'] ?><br/><?= $item['date'] ?></p>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<? } ?>