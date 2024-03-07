<!DOCTYPE html>
<html lang="ru">
    <?= app()->chunk()->render('common.head') ?>
    <body>
        <div class="order-container">
            <div class="order-container-wrapper">
                <?= app()->chunk()->render('common.header_cart') ?>
                <div class="order-content">
                    <?= isset($content) ? $content : '' ?>
                </div>
            </div>
            <?= app()->chunk()->render('common.footer') ?>
            <?= app()->chunk()->render('common.foot') ?>
        </div>
    </body>
</html>