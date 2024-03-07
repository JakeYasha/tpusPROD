<?define('APP_IS_LK_FIRM_USER', true);?>
﻿<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('firmuser.head')?>
	<body>
		<div class="container container_index">
			<?= app()->chunk()->render('common.old_header')?>
			<?= app()->chunk()->render('firm_user.sidebar')?>
			<div class="content">
				<?= isset($content) ? $content : ''?>
			</div>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.old_foot')?>
		</div>
	</body>
</html>