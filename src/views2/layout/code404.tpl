<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8" />
		<title>404</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/favicon.ico" rel="shortcut icon" />
		<link rel="stylesheet" type="text/css" href="/css/client.main.css" />
		<link rel="stylesheet" type="text/css" href="/css/client.extended.css" />
		<link rel="stylesheet" type="text/css" href="/css/mobile.css" />
	</head>
	<body>
		<div class="container">
			<div class="for_clients">
				<div class="for_clients_text_c clearfix page">
					<div class="page-404">
						<p><img style="margin: 0 auto; display: inherit; max-width: 100%;" src="/img/404.png"></p>
						<h1>Ошибка 404. Cтраница не найдена :(</h1>
						<p>Искомая страница была перемещена или удалена. Приносим извинения. Предлагаем следующие варианты:</p>
						<ul>
							<li>Перейти на <a href="/">главную страницу</a></li>
							<li>Посмотреть <a href="/catalog/">каталог товаров</a></li>
							<li>или <a href="/catalog/44/">каталог услуг</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</body>
</body>
</html>
<?
/*if (!app()->stat()->isBot() && !!app()->stat()->isDeveloper() && str()->index(app()->request()->getRequestUri(), '.jpg') === false && str()->index(app()->request()->getRequestUri(), '.gif') === false && str()->index(app()->request()->getRequestUri(), '.png') === false) {
	$text = [];
	$text[] = app()->request()->getRequestUri();
	$text[] = app()->request()->getRequestMethod();
	$text[] = app()->request()->getUserAgent();
	file_put_contents(APP_DIR_PATH . '/app/cron/log/errors.log', PHP_EOL . date('d.m.Y H:i:s') . ' - ' . implode(' - ', $text), FILE_APPEND);
}*/
?>