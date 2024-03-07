<?if($items){?>
    <?foreach ($items as $k => $video) {$firm = new \App\Model\Firm();$firm->getByIdFirm($video->id_firm());?>
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
<?}?>