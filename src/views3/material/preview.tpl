<? if ($item) {?>
    <div class="article-page__title-block">
        <div class="article-page__tag">Политика</div>
        <h1><?= $item->name() ?></h1>
        <div class="article-page__actions"><span class="article__info article__info_article-page"><?=$item->val('timestamp_last_updating');?></span><a class="share" href="">Поделиться<img src="/img/share.png"></a></div>
    </div>
<?
if ($item->val('image'))
{
?>
    <div class="article-page__img">
        <img class="img-fluid" src="<?=$item->val('image');?>">
    </div>
<?
}
?>
    <div class="article-page__content">
        <div class="article-page__accent"><?= $item->val('short_text') ? $item->val('short_text') : 'title' ?></div>
        <?= $item->val('text') ? $item->val('text') : 'Текст материала еще не был сгенерирован' ?>
        <div class="read-more module">
            <p class="read-more__heading">Читайте также</p>
            <ul class="list read-more__list">
                <li class="read-more__list--item"><a class="read-more__list--link" href="">В Израиле объяснили, почему едва не сорвалось принятие закона о «доме еврейского народа»</a></li>
                <li class="read-more__list--item"><a class="read-more__list--link" href="">В Израиле объяснили, почему едва не сорвалось принятие закона о «доме еврейского народа»</a></li>
                <li class="read-more__list--item"><a class="read-more__list--link" href="">В Израиле объяснили, почему едва не сорвалось принятие закона о «доме еврейского народа»</a></li>
            </ul>
        </div>
        <!--div class="comments module">
            <form class="form_comment">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone">
                        <input class="form__control form__control_input-lg" placeholder="Добавьте свой комментарий">
                    </div>
                    <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                        <button class="btn btn_primary btn_comments">ОТПРАВИТЬ</button>
                    </div>
                </div>
            </form>
            <div class="comments__counter module">Коммментарии (56)</div>
            <ul class="list comments-list">
                <li class="comments-list__item">
                    <div class="comments-list__profile--img"><img src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"></div>
                    <div class="comments-list__profile--info"><span class="comments-list__profile--name">Genry Hulper</span><span class="comments-list__time">4 дня назад</span>
                        <p class="comments-list__text">Очень интересно! Что же будет дальше?</p>
                    </div>
                </li>
                <li class="comments-list__item">
                    <div class="comments-list__profile--img"><img src="https://images.unsplash.com/photo-1568734021492-e13c8c1cd879?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=500&amp;q=60"></div>
                    <div class="comments-list__profile--info"><span class="comments-list__profile--name">Genry Hulper</span><span class="comments-list__time">4 дня назад</span>
                        <p class="comments-list__text">Очень интересно! Что же будет дальше?</p>
                    </div>
                </li>
            </ul>
        </div-->
    </div>
<? } else { ?>
    Статьи, увы нет :(
<? } ?>