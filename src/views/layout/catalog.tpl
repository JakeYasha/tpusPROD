<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body>
		<div class="container container_index">
			<?= app()->chunk()->render('common.header')?>
			<?= app()->chunk()->render('common.sidebar')?>
			<div class="content <?if(app()->sidebar()->getParam('right_layout_filter_sidebar') !== null){?> filter-sidebar<?}?>">
                                <?= app()->chunk()->render('adv.header_banners')?>

				<?= isset($content) ? $content : ''?>
			</div>
			<?= app()->chunk()->render('common.direct')?>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
	</body>
</html>