<?foreach ($items as $k => $video) {?>
	<article class="article article-card">
        <div class="article__img">
            <a href="<?=$video->link()?>"><img class="img-fluid" style="min-height: 100%;" src="<?=$video->getThumbnailSrc()?>" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>">
            <span class="article__tag"><?=$video->val('video_length') ? $video->val('video_length') : 'ВИДЕО' ?></span></a>
        </div>
        <div class="article__info article__info_special"><?=$video->val('total_views')?> <?=  \CWord::ending((int)$video->val('total_views'), ['просмотр','просмотра','просмотров'])?></div>
        <a class="article__heading article-card__heading article__heading_special" href="<?=$video->link()?>"><?=$video->name()?></a>
	</article>
<?}?>