<div class="popular module">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-8-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
            <div class="block-title">
                <div class="block-title__decore block-title__decore_light"></div>
                <h2 class="block-title__heading">Популярное</h2>
            </div>
            <div class="popular-list">
                
                <? foreach($last_popular_materials as $last_popular_material) {?>
                    <article class="article article_popular">
                        <div class="mdc-layout-grid__inner">
                            <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-1 mdc-layout-grid__cell--order-md-2"><a class="article__heading article__heading_light" href="<?=$last_popular_material['link']?>"><?=$last_popular_material['name']?></a>
                                <div class="article__info article__info_end"><?=$last_popular_material['time_beginning']?></div>
                            </div>
                            <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--order-2 mdc-layout-grid__cell--order-md-1">
                                <div class="article__img"><img class="img-fluid" src="<?=$last_popular_material['image'] ? $last_popular_material['image']->iconLink('-thumb') : ''?>"></div>
                            </div>
                        </div>
                    </article>
                <? } ?>
            </div>
        </div>
        
        <div class="mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone"><img class="img-fluid banner-hidden-md " src="/public/img3/newspapperimg.png"></div>
        
    </div>
</div>
<div class="articles module">
    <div class="mdc-layout-grid__inner">
        <? foreach($last_recommend_materials as $last_recommend_material) {?>
            <div class="mdc-layout-grid__cell">
                <article class="article article-card">
                    <div class="article__img"><img class="img-fluid" src="<?=$last_recommend_material['image'] ? $last_recommend_material['image']->iconLink('-thumb') : ''?>"><span class="article__tag"><?=$last_recommend_material['rubric']?></span></div><a class="article__heading article-card__heading" href="<?=$last_recommend_material['link']?>"><?=$last_recommend_material['name']?></a>
                    <div class="article__info">Источник: <?=$last_recommend_material['material_source_name']?></div>
                </article>
            </div>
        <? } ?>
    </div>
</div>
<!--div class="btn-container">
    <button class="btn btn_load-more"><img src="/img/reload.png"><span>Показать еще</span></button>
</div-->