<div class="top_review" style="display: inline-block;">
	<div class="rate">
		<span class="stars" style="width: <?=((int)($score*16))?>px;"></span>
	</div>
	<?if(!$only_stars && $count > 0) {?>
                <a href="<?=$url?>" class="more_reviews more_reviews_mob"><?=$count?> <?=  \CWord::ending($count, ['отзыв','отзыва','отзывов'])?></a>
	<?} else if($firm != null) {?>
                <a class="more_reviews more_reviews_mob fancybox fancybox.ajax" href="/firm-review/get-add-form/<?=$firm->id()?>/" rel="nofollow">Добавить отзыв</a>
	<?}?>
</div>