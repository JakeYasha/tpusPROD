﻿<!DOCTYPE html>
<html lang="ru">
    <?= app()->chunk()->render('common.head') ?>
    <body>
        <div class="container container_index">
            <?= app()->chunk()->render('common.common_header')?>
            <?= app()->chunk()->render('common.sidebar') ?>
            <div class="content">
                <div class="page__container">
                    <?= isset($content) ? $content : '' ?>
                </div>
            </div>
            <?= app()->chunk()->render('common.footer') ?>
            <?= app()->chunk()->render('common.foot') ?>
        </div>
    </body>
</html>