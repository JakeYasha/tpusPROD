<?/** FirmReview $it*/?>
<?if($items){?>
<div class="last_reviews">
	<h2>Новые отзывы о фирмах</h2>
	<?  foreach ($items as $item){?>
	<div class="review">
		<div class="top_review">
			<a href="<?=$item['firm']->link()?>" class="company_name"><?=$item['firm']->name()?></a>
			<?= app()->chunk()->setArgs([$item['firm'], true])->setVar('style','display:inline-block;')->render('rating.stars')?>
			<?/*<a href="#" class="more_reviews">3 отзыва</a>*/?>
		</div>
		<div class="main_review">
			<div class="name_date">
				<?= app()->chunk()->setArgs([$item['score'], true])->render('rating.stars')?><br/>
				<span class="name"><?=$item['user']?></span>, <span class="date"><?=$item['date']?>:</span>
			</div>
			<p><?=strip_tags($item['text'])?></p>
		</div>
	</div>
	<?}?>
</div>
<?}?>