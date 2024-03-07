<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set("log_errors", 0);

try {
    $actions = [
        new \App\Action\Crontab\CatalogCounter(),
        new \App\Action\Crontab\Cleaner(),
        new \App\Action\Crontab\Sitemap(),
        new \App\Action\Crontab\Notifier()
    ];

    foreach ($actions as $action) {
        $action->execute();
    }
} catch (Exception $e) {
    $email = app()->email()
            ->email()
            ->clearRecipients()
            ->clearReplyTo()
            // ->setFrom('site@tovaryplus.ru', 'Товары плюс')
            ->addTo('worktablepro@gmail.com')
            ->setSubject('Обновление (2 часть) завершено с ошибками')
            ->setHtmlText('Обновление завершено с ошибками в ' . date('d.m.Y H:i') . ': ' . $e->getMessage())
            ->send();
} finally {
    $email = app()->email()
            ->email()
            ->clearRecipients()
            ->clearReplyTo()
            // ->setFrom('site@tovaryplus.ru', 'Товары плюс')
            ->addTo('worktablepro@gmail.com')
            ->setSubject('Обновление (2 часть) завершено')
            ->setHtmlText('Обновление завершено в ' . date('d.m.Y H:i'))
            ->send();
}