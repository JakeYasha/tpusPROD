<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell--span-12">
        <?= $tabs ?>
        <? if ($items) { ?>
            <div class="comments module">
                <div class="form_comment">
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--align-middle">
                            <h2><?= app()->metadata()->getTitle() ?></h2>
                        </div>
                        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-4-phone">
                            <a rel="nofollow" class="btn btn_primary btn_comments" href="/firm-review/get-add-form/<?=$firm->id()?>/">Добавить отзыв</a>
                        </div>
                    </div>
                </div>
                <ul class="list comments-list js-next-reviews-holder">
                    <? foreach ($items as $item) { ?>
                        <li class="comments-list__item">
                            <div class="comments-list__profile--img"><img src="/css/img/no_img.png" alt="Нет фотографии"></div>
                            <div class="comment-list-content">
                                <div class="comments-list__profile--info"><span class="comments-list__profile--name"><?= $item['user'] ?></span><span class="comments-list__time"><?= $item['date'] ?></span>
                                    <p class="comments-list__text"><?= strip_tags($item['text']) ?></p>
                                </div>
                            </div>
                        </li>
                    <? } ?>
                </ul>
                <? if ($has_next) { ?>
                    <a class="comments__counter module btn-show-more js-next-btn js-action" data-holder=".js-next-reviews-holder" data-url="<?= $firm->link() ?>?mode=review&ajax=1" data-page="1" href="#">показать еще</a>
                <? } ?>
            </div>
        <? } ?>
        <div class="module stat-info">
            <div>Комментарии и отзывы посетителей сайта TovaruPlus.ru являются выражением их личного мнения. Администрация сайта TovaruPlus.ru: 
                <ul class="list read-more__list product-list">
                    <li class="read-more__list--item">не несет ответственности за отзывы и комментарии посетителей сайта TovaruPlus.ru</li>
                    <li class="read-more__list--item">не несет обязанности проверять достоверность сведений об обстоятельствах, на которых основаны мнения посетителей сайта TovaruPlus.ru</li>
                    <li class="read-more__list--item">не вправе ограничивать конституционные права посетителей сайта TovaruPlus.ru, в том числе свободу слова, а также право передачи и распространения информации, если реализация данных прав не противоречит закону.</li>
                </ul>
            </div>
        </div>
        <br/>
    </div>
</div>