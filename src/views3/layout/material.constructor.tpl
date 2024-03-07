<?define('APP_IS_LK_MANAGER', true);?>
<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('firmuser.head')?>
	<body>
		<div class="constructor-container">
			<div class="constructor-container-wrapper">
				<?= app()->chunk()->render('common.old_header')?>
				
				<?= isset($content) ? $content : ''?>
			</div>

			<div class="footer" style="display:none;"></div>
			<?= app()->chunk()->render('common.old_foot')?>
		</div>
	</body>
</html>