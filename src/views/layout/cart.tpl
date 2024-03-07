<!DOCTYPE html>
<html lang="ru">
    <?= app()->chunk()->render('common.head') ?>
    <body>
        <div class="cart-container">
            <div class="cart-container-wrapper">
                <?= app()->chunk()->render('common.header_cart') ?>
                <div class="cart-content">
                    <?= isset($content) ? $content : '' ?>
                </div>
            </div>
            <?= app()->chunk()->render('common.footer') ?>
            <?= app()->chunk()->render('common.foot') ?>
        </div>
    </body>
</html>