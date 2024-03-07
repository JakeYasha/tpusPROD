<div class="top_review">
    <? if (!$only_stars && $count > 0) { ?>
        <a href="<?= $url ?>" class="more_reviews more_reviews_mob"><?= $count ?> <?= \CWord::ending($count, ['отзыв', 'отзыва', 'отзывов']) ?></a>
    <? } else if ($firm != null) { ?>
        <a class="more_reviews more_reviews_mob fancybox fancybox.ajax" href="/firm-review/get-add-form/<?= $firm->id() ?>/" rel="nofollow">Добавить отзыв</a>
    <? } ?>
</div>