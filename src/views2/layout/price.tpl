<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body>
		<div class="container container_index">
			<?= app()->chunk()->render('common.header')?>
			<?= app()->chunk()->render('common.sidebar')?>
			<div class="content">
                <?= app()->chunk()->render('adv.header_banners')?>
                                <?/*if (strpos(app()->uri(), '/page/show/novogodnie-i-rozhdestvenskie-skidki-v-yaroslavle.htm') === FALSE) { ?>
                                    <?= app()->chunk()->render('adv.header_banners')?>                                
                                <?}*/?>
				<?= isset($content) ? $content : ''?>
			</div>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
	</body>
</html>