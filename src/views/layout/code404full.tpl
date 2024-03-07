<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body>
		<div class="container container_index">
			<?= app()->chunk()->render('common.header')?>
			<?= app()->chunk()->render('common.sidebar')?>
			<div class="for_clients">
				<div class="for_clients_text_c clearfix page full404">
					<?= isset($content) ? $content : ''?>
				</div>
			</div>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
	</body>
</html>
<?
/*if (!app()->stat()->isBot() && app()->stat()->isDeveloper() && str()->index(app()->request()->getRequestUri(), '.jpg') === false && str()->index(app()->request()->getRequestUri(), '.gif') === false && str()->index(app()->request()->getRequestUri(), '.png') === false) {
	$text = [];
	$text[] = app()->request()->getRequestUri();
	$text[] = app()->request()->getRequestMethod();
	$text[] = app()->request()->getUserAgent();
	file_put_contents(APP_DIR_PATH . '/app/cron/log/errors.log', PHP_EOL . date('d.m.Y H:i:s') . ' - ' . implode(' - ', $text), FILE_APPEND);
}*/
?>