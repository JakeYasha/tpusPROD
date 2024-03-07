<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body class="page">
		<div class="container container_index">
			<?= app()->chunk()->render('common.materials_header')?>
			<?= app()->chunk()->render('common.sidebar')?>
			<div class="content">
				<?= isset($content) ? $content : ''?>
			</div>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
                <?/*if (app()->location()->currentId() === '76004') {?>
                <div class="left-side-popup2" >
                    <a href="#" class="close"></a>
                    <img class="image-20-years" src="/img/20_years(2).png" style="">
                </div>
                <?}*/?>
	</body>
</html>
