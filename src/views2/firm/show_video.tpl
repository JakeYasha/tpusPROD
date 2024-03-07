<div class="item_info">
    <div class="search_result">
        <?= $tabs ?>
        <? if ($items || $item) { ?>
            <div class="comp_video">
                <div class="video_item">
                    <div class="video_item_frame">
                        <? if ($item->isYoutube()) { ?>
                            <iframe style="height: 100%;width: 100%;border: none;" src="https://www.youtube.com/embed/<?= $item->getYoutubeHash() ?>" allowfullscreen></iframe>
                        <? } else { ?>
                            <?= $item->val('video_code') ?>
                        <? } ?>
                    </div>
                    <div class="video_about">
                        <h2><?= $item->name() ?></h2>
                        <pre><?= strip_tags($item->val('text')) ?></pre>
                        <hr/>
                        <span><?= $item->val('total_views') ?> <?= \CWord::ending((int) $item->val('total_views'), ['просмотр', 'просмотра', 'просмотров']) ?></span>
                    </div>
                    <div class="reviews">
                        <? if ($reviews) { ?>
                            <div class="last_reviews">
                                <a class="head_h2" href="<?= app()->linkFilter($url, [], ['mode' => 'review']) ?>">Отзывы</a>
                                <a rel="nofollow" class="more_review fancybox fancybox.ajax" href="/firm-review/get-add-form/<?= $item->id() ?>/">Добавить отзыв</a>
                                <? foreach ($reviews as $rev) { ?>
                                    <div class="review">
                                        <div class="main_review">
                                            <?= app()->chunk()->setArgs([$rev['score'], true])->render('rating.stars') ?><br/>
                                            <div class="name_date">
                                                <span class="name"><?= $rev['user'] ?></span>, <span class="date"><?= $rev['date'] ?>:</span>
                                            </div>
                                            <p><?= strip_tags($rev['text']) ?></p>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        <? } else { ?>
                            <div class="last_reviews">
                                <a class="head_h2" href="<?= app()->linkFilter($url, [], ['mode' => 'review']) ?>">Отзывы</a>
                                <a rel="nofollow" class="more_review fancybox fancybox.ajax" href="/firm-review/get-add-form/<?= $item->id() ?>/">Добавить отзыв</a>
                                <a rel="nofollow" class="fancybox fancybox.ajax" href="/firm-review/get-add-form/<?= $item->id() ?>/"><div class="red-block">Для этой фирмы пока нет отзывов, поделитесь своим!</div></a>
                            </div>
                        <? } ?>
                    </div>
                </div><div class="video_items video_show">
                    <? foreach ($items as $k => $video) { ?>
                        <div class="video">
                            <div class="video_field">
                                <a href="<?= app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()]) ?>"><img src="<?= $video->getThumbnailSrc() ?>" alt="<?= str()->replace($video->name(), ['"'], ['&quot;']) ?>"></a>
                                <span><?= $video->val('video_length') ?></span>
                            </div><div class="video_text">
                                <a href="<?= app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()]) ?>"><?= $video->name() ?></a>
                                <span class="video_text_views"><?= $video->val('total_views') ?> <?= \CWord::ending((int) $video->val('total_views'), ['просмотр', 'просмотра', 'просмотров']) ?></span>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
        <? } ?>
    </div>
</div>