<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();

$is_update = false;

try {
    // $start_action = new \App\Action\Crontab\SyncFirm();
    // $is_update = $start_action->execute();
//
    $actions = [];

    $actions = array_merge($actions, [
        new \App\Action\Crontab\StsStatistics(),
        new \App\Action\Crontab\CurrentRegionStats(),
        new \App\Action\Crontab\AvgStatisticsMaker(),
        new \App\Action\Crontab\AvgStatistics727373Maker(),
        new \App\Action\Crontab\SiteStatistics(),
    ]);

    foreach ($actions as $action) {
        $action->execute();
    }
} catch (Exception $e) {
    $email = app()->email()
            ->email()
            ->clearRecipients()
            ->clearReplyTo()
            ->setFrom('site@tovaryplus.ru', 'Товары плюс')
            ->addTo('vae@727373.ru')
            ->setSubject('Обновление Статистики завершено с ошибками')
            ->setHtmlText('Завершено с ошибками в ' . date('d.m.Y H:i') . ': ' . $e->getMessage())
            ->send();
} finally {
    $email = app()->email()
            ->email()
            ->clearRecipients()
            ->clearReplyTo()
            ->setFrom('site@tovaryplus.ru', 'Товары плюс')
            ->addTo('vae@727373.ru')
            ->setSubject('Обновление статистики завершено')
            ->setHtmlText('Обновление завершено в ' . date('d.m.Y H:i'))
            ->send();
}