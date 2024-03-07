<? if ($items) { ?>
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
<?
}?>