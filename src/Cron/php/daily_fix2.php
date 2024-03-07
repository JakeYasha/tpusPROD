<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();

$is_update = false;

try {
    $start_action = new \App\Action\Crontab\SyncFirm();
    $is_update = $start_action->execute();
//
    $actions = [];

    if ($is_update) {
        $actions = array_merge($actions, [
            new \App\Action\Crontab\SyncPrice(),
            new \App\Action\Crontab\SyncImages(),
            new \App\Action\Crontab\FirmTyper(),
            new \App\Action\Crontab\FirmRating(),
                //new \App\Action\Crontab\BrandMaker(),
        ]);
    }

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
            ->setSubject('Обновление (1 часть) завершено с ошибками')
            ->setHtmlText('Обновление (' . ($is_update ? 'файлы обновления найдены' : 'файлы обновления НЕ БЫЛИ найдены') . ') завершено с ошибками в ' . date('d.m.Y H:i') . ': ' . $e->getMessage())
            ->send();
} finally {
    $email = app()->email()
            ->email()
            ->clearRecipients()
            ->clearReplyTo()
            ->setFrom('site@tovaryplus.ru', 'Товары плюс')
            ->addTo('vae@727373.ru')
            ->setSubject('Обновление (1 часть) завершено')
            ->setHtmlText('Обновление (' . ($is_update ? 'файлы обновления найдены' : 'файлы обновления НЕ БЫЛИ найдены') . ') завершено в ' . date('d.m.Y H:i'))
            ->send();
}