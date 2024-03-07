<header>
    <?= app()->chunk()->render('common.header_tp_logo') ?>
    <div class="header-top">
        <?= app()->chunk()->render('cart.preview') ?>
        <? if (app()->firmManager()->exists()) { ?>	
            <a href="/firm-manager/" rel="nofollow" class="entry"><?= app()->firmManager()->val('email') ?></a>
            <a href="/firm-manager/logout/" rel="nofollow" class="entry-exit">Выход</a>
        <? } elseif (app()->firmUser()->exists()) { ?>
            <a href="/firm-user/" rel="nofollow" class="entry"><?= app()->firmUser()->val('email') ?></a>
            <a href="/firm-user/logout/" rel="nofollow" class="entry-exit">Выход</a>
        <? } else { ?>
            <a href="/firm-user/get-login-form/" class="entry fancybox fancybox.ajax js-login-form-btn" rel="nofollow">Войти в кабинет</a>
        <? } ?>
    </div>
    <div class="header-bottom">
        <div class="header-bottom-left">
            <div class="search-block">
                <form action="<?= $search_settings['action'] ?>" method="get" class="js-form js-main-search-frm" data-city="<?= $search_settings['city'] ?>" data-mode="<?= $search_settings['mode'] ?>">
                    <div class="extra-options second-line js-search-mode-selector">
                        <span><?= $search_modes[$search_settings['mode']] ?></span>
                        <ul>
                            <? foreach ($search_modes as $key => $mode) { ?>
                                <li><a href="#" class="js-action" data-mode="<?= $key ?>" data-name="<?= $mode ?>"><?= $mode ?></a></li>
                            <? } ?>
                        </ul>
                    </div>
                    <?= $autocomplete ?>
                    <div class="extra-options first-line js-search-city-selector">
                        <span><?= app()->location()->currentName() ?></span>
                    </div>
                    <button type="submit" class="sprite-sear"></button>
                </form>
            </div>
        </div>
        <div class="header-bottom-right">
            <a href="/request/add/" rel="nofollow" class="bubble_button">Добавить фирму</a>
        </div>
    </div>
    <div class="search320">
        <a href="#" class="js-search-toggle js-action"></a>
    </div>
    <div class="menu320">
        <a href="#"></a>
    </div>
</header>
<? /*
  <div class="header">
  <?=  app()->chunk()->render('common.header_tp_logo')?>
  <div class="search_field">
  <form action="<?=app()->link('/search/')?>" method="get" class="js-form js-main-search-frm">
  <?=$autocomplete?>
  <select>
  <option>Товары и услуги</option>
  <option>Компании</option>
  </select>
  <select>
  <option>Ярославль</option>
  <option>Кострома</option>
  </select>
  <button type="submit" class="sprite-sear js-main-search-btn js-send"></button>
  </form>
  </div>
  <?if(app()->firmManager()->exists()){?>
  <div class="exit_field">
  <div class="mail_field">
  <a href="/firm-manager/" rel="nofollow"><?=app()->firmManager()->val('email')?></a>
  </div>
  <div class="exit">
  <a href="/firm-manager/logout/" rel="nofollow">Выход</a>
  </div>
  </div>
  <?} elseif(app()->firmUser()->exists()){?>
  <div class="exit_field">
  <div class="mail_field">
  <a href="/firm-user/" rel="nofollow"><?=app()->firmUser()->val('email')?></a>
  </div>
  <div class="exit">
  <a href="/firm-user/logout/" rel="nofollow">Выход</a>
  </div>
  </div>
  <?} else {?>
  <div class="add_field">
  <a href="/request/add/" class="add">Добавить организацию</a>
  </div>
  <div class="login_field">
  <a href="/firm-user/get-login-form/" class="login fancybox fancybox.ajax js-login-form-btn" rel="nofollow">Войти</a>
  <a style="display: none;" href="/firm-user/get-restore-form/" class="fancybox fancybox.ajax js-recover-form-btn" rel="nofollow">Напомнить пароль</a>
  </div>
  <?}?>
  <div class="menu320">
  <a href="#"></a>
  </div>
  </div>
 * */ ?>
 