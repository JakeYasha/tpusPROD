<?=$bread_crumbs?>
<div class="cat_description">
	<?if($filters['success_add']){?>
	<p style="font-size: 140%; color: green; text-align: center; padding: 20px; border: 2px solid green;">Фотография успешно добавлена! После модерации она появится на этой странице.</p>
	<?}?>
	<h1><?=$item['name']?></h1>
	<?=$item['brief_text']?>
	<p></p>
    <p><a href="https://www.instagram.com/tovaryplus/" target="_blank" rel="nofollow"><img src="/uploaded/d/2/d2b28af772dc7a3d75c1436df88baeaa.png" alt="" width="50" height="51" style="float: left; margin: 10px;" /></a> <strong>А так же голосуйте за фотографии в нашем аккаунте в Instagram!</strong> <br />Все фотографии, выставленные для участия в нашем фотоконкурсе будут дополнительно выставлены на официальной странице Информационного центра "Товары плюс" &nbsp;<a href="https://www.instagram.com/tovaryplus/" target="_blank"  rel="nofollow">instagram.com/tovaryplus</a></p>
    <p>При подведении итогов все "лайки" под фотографиями в Instagram будут добавлены к голосам на сайте!</p>
	<p>Пригласи друзей на наш конкурс, поделись ссылкой!</p>
</div>
<div style="margin-left:22px">
	<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
	<script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
	<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki" data-counter=""></div>
</div>
<?if($item['has_winner']){?>
	<div class="photo-contest-nomination-list">
		<span class="nl-name red cap">Поздравляем победителей фотоконкурса</span>
		<div class="cat_description">
			<?=$item['text_winner']?>
		</div>
		<ul>
		<?foreach ($winners as $photo){?>
			<li>
				<div class="shell-a"><a class="fancybox" data-fancybox-group="gallery" href="<?=$photo['big_image_url']?>"><img src="<?=$photo['image_url']?>" alt="" /></a></div>
				<span class="person-name">автор:<span><?=$photo['name']?></span></span>
				<span class="votes-number"><?=$photo['nomination']?></span>
			</li>
		<?}?>
		</ul>
	</div>
<?}?>
<div class="search_result">
	<?=$tabs?>
</div>
<div class="item_info photo-contest">
	<div class="cat_description">
		<?=$tab_text?>
	</div>
	<?if($nominations && !$filters['mode']){?>
		<div class="photo-contest-nominations">
			<h2 class="w100 center">Номинации конкурса</h2>
			<div class="left">
				<ul>
					<?foreach ($nominations as $nom){?>
					<li<?if($nom['active']){?> class="active"<?}?>><a href="<?=$nom['link']?>"><img src="<?=$nom['image_url']?>" alt="" /></a></li>
					<?}?>
				</ul>
			</div>
			<div class="right">
				<ul>
					<?foreach ($nominations as $nom){?>
					<li <?if($nom['active']){$active_nomination_id = $nom['id'];?>class="active"<?}?>>
						<a href="<?=$nom['link']?>"><img src="<?=$nom['image_url']?>" alt="" /></a>
						<a href="<?=$nom['link']?>"><?=$nom['name']?></a>
					</li>
					<?}?>
				</ul>
			</div>
		</div>
	<?}?>
	<?if($item['banners']){?>
	<div class="search_result">
	<?=app()->adv()->renderBannerPlace($item['banners'])?>
	<?}?>
	</div>
	<?if(!$filters['mode']){?>
	<div class="photo-contest-nomination-list">
		<span class="nl-name">Номинация: <?=  str()->toLower($active_nomination_name)?></span>
		<?if($item['working'] && !$item['finished']){?>
		<a href="/photo-contest/add-photo/<?=$item['id']?>/?nomination=<?=$active_nomination_id?>" class="btn-simple icon-plus">Добавить фотографию</a>
		<?}?>
		<?if($photos){?>
		<ul>
			<?foreach ($photos as $photo){?>
			<li>
				<div class="shell-a"><a class="fancybox" data-fancybox-group="gallery" href="<?=$photo['big_image_url']?>"><img src="<?=$photo['image_url']?>" alt="фото участника конкурса" /></a></div>
				<?if($item['finished'] || !$item['working'] || !PhotoContest::userCanVote($item['id'], $active_nomination_id)){?>
					<span class="person-name">автор:<span><?=$photo['name']?></span></span>
					<span class="votes-number"><?=$photo['counter_votes']?> <?=\CWord::ending($photo['counter_votes'], ['голос','голоса','голосов'])?></span>
				<?} else {?>
					<a href="#" data-id="<?=$photo['id']?>" class="btn-simple js-action js-photo-contest-vote">голосовать</a>
					<div class="hidden js-photo-contest-result">
						<span class="person-name">автор:<span><?=$photo['name']?></span></span>
						<span class="votes-number js-photo-contest-votes"><?=$photo['counter_votes']?> <?=\CWord::ending($photo['counter_votes'], ['голос','голоса','голосов'])?></span>
					</div>
				<?}?>
			</li>
			<?}?>
		</ul>
		<?}?>
	</div>
	<?}?>
</div>
<?if($item['text']){?>
<div class="cat_description">
	<?=$item['text']?>
</div>
<?}?>