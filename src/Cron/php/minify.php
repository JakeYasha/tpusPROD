<?php


require_once '/var/www/sites/tovaryplus.ru/config/config.php';
$path = LIBS_DIR_PATH . '/minify';
require_once $path . '/minify/src/Minify.php';
require_once $path . '/minify/src/CSS.php';
require_once $path . '/minify/src/JS.php';
require_once $path . '/minify/src/Exception.php';
require_once $path . '/path-converter/src/Converter.php';

use MatthiasMullie\Minify;

/* <?= html()->cssFile('/css/client.main.css?v='.time())?>
  <?= html()->cssFile('/css/client.extended.css?v='.time())?>
  <?= html()->cssFile('/css/mobile.css')?>
  <?= html()->cssFile('/css/jquery.fancybox.css')?>
  <?= html()->cssFile('/css/slick.css')?>
  <?= html()->cssFile('/css/jquery-ui-1.10.4.custom.min.css', 'screen')?> */

$minifier = new Minify\CSS();
$minifier->add(APP_DIR_PATH . '/public/css/client.main.css');
$minifier->add(APP_DIR_PATH . '/public/css/client.extended.css');
$minifier->add(APP_DIR_PATH . '/public/css/mobile.css');
$minifier->add(APP_DIR_PATH . '/public/css/jquery.fancybox.css');
$minifier->add(APP_DIR_PATH . '/public/css/slick.css');
$minifier->add(APP_DIR_PATH . '/public/css/jquery-ui-1.10.4.custom.min.css');

$minifier->minify(APP_DIR_PATH . '/public/css/styles.min.css');

if (isset($argv[1]) && $argv[1] === 'css') {
	exit();
}
$minifier = new Minify\JS();
$minifier->add(APP_DIR_PATH . '/public/js/jquery-1.11.0.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.slides.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.formstyler.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.qtip.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.autosuggest.js');
$minifier->add(APP_DIR_PATH . '/public/js/js.js');
$minifier->add(APP_DIR_PATH . '/public/js/imagesloaded.pkgd.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/masonry.pkgd.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/common.js');
$minifier->add(APP_DIR_PATH . '/public/js/cart.js');
$minifier->add(APP_DIR_PATH . '/public/js/sky/plugins/jquery-ui-1.10.4.custom/jquery-ui-1.10.4.custom.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/sky/plugins/jquery-ui-1.10.4.custom/jquery-ui-datepicker-ru.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.jcarousel.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.fancybox.pack.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.maskedinput.min.js');
$minifier->add(APP_DIR_PATH . '/public/js/sky/common/js/utils.js');
$minifier->add(APP_DIR_PATH . '/public/js/sky/common/js/form.js');
$minifier->add(APP_DIR_PATH . '/public/js/sky/common/js/validator.js');
$minifier->add(APP_DIR_PATH . '/public/js/jquery.autosize.min.js');

$minifier->minify(APP_DIR_PATH . '/public/js/js.min.js');
