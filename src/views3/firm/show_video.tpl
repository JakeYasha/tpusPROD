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
                            <div class="comments module">
                                <div class="form_comment">
                                    <div class="mdc-layout-grid__inner">
                                        <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--align-middle">
                                            <h2>Отзывы</h2>
                                        </div>
                                        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                                            <a rel="nofollow" class="btn btn_primary btn_comments" href="/firm-review/get-add-form/<?=$firm->id()?>/">Добавить отзыв</a>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list comments-list">
                                    <? foreach ($reviews as $rev) { ?>
                                        <li class="comments-list__item">
                                            <div class="comments-list__profile--img"><img  src="/css/img/no_img.png" alt="Нет фотографии"></div>
                                            <div class="comment-list-content">
                                                <div class="comments-list__profile--info"><span class="comments-list__profile--name"><?= $rev['user'] ?></span><span class="comments-list__time"><?= $rev['date'] ?></span>
                                                    <p class="comments-list__text"><?= strip_tags($rev['text']) ?></p>
                                                    <?/*= app()->chunk()->setArgs([$rev['score'], true])->render('rating.stars') */?>
                                                </div>
                                            </div>
                                        </li>
                                    <? } ?>
                                </ul>
                            </div>
                            
                        <? } else { ?>
                            <div class="comments module">
                                <div class="form_comment">
                                    <div class="mdc-layout-grid__inner">
                                        <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--align-middle">
                                            <h2>Отзывы</h2>
                                        </div>
                                        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                                            <a rel="nofollow" class="btn btn_primary btn_comments" href="/firm-review/get-add-form/<?=$firm->id()?>/">Добавить отзыв</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
                <div class="special module">
                    <div class="mdc-layout-grid__inner">
                        <?foreach ($items as $k => $video) {?>
                            <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
                                <article class="article article-card">
                                    <div class="article__img">
                                        <a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>">
                                            <img class="img-fluid" src="<?=$video->getThumbnailSrc()?>" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>">
                                            <span class="article__tag"><?=$video->val('video_length')?></span>
                                        </a>
                                    </div>
                                    <div class="article__info article__info_special"><?=$video->val('total_views')?> <?=  \CWord::ending((int)$video->val('total_views'), ['просмотр','просмотра','просмотров'])?></div>
                                    <a class="article__heading article-card__heading article__heading_special" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><?=$video->name()?></a></p>
                                </article>
                            </div>
                        <?}?>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>