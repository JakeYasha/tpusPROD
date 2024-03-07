<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell--span-12">
		<?= $tabs?>
		<? if ($items) { ?>
            <h2><?=app()->metadata()->getHeader()?></h2>
            <div class="special module">
                <div class="mdc-layout-grid__inner">
                    <?foreach ($items as $k => $video) {?>
                        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
                            <article class="article article-card">
                                <div class="article__img">
                                    <a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>">
                                        <img class="img-fluid" src="<?=$video->getThumbnailSrc()?>" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>">
                                        <span class="article__tag"><?=$video->val('video_length') ? $video->val('video_length') : 'ВИДЕО' ?></span>
                                    </a>
                                </div>
                                <div class="article__info article__info_special"><?=$video->val('total_views')?> <?=  \CWord::ending((int)$video->val('total_views'), ['просмотр','просмотра','просмотров'])?></div>
                                <a class="article__heading article-card__heading article__heading_special" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><?=$video->name()?></a></p>
                            <!-- ....asa  -->
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