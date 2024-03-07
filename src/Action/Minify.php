<?php

namespace App\Action;
$path = LIBS_DIR_PATH . '/minify';

require_once $path . '/minify/src/Minify.php';
require_once $path . '/minify/src/CSS.php';
require_once $path . '/minify/src/JS.php';
require_once $path . '/minify/src/Exception.php';
require_once $path . '/minify/src/Exceptions/BasicException.php';
require_once $path . '/minify/src/Exceptions/IOException.php';
require_once $path . '/path-converter/src/Converter.php';

class Minify extends \App\Classes\Action {

	public function execute() {
//		if (!APP_IS_DEV_MODE) {
//			throw new \Sky4\Exception();
//		}

		$minifier = new \MatthiasMullie\Minify\CSS();
		$minifier->setMaxImportSize(0);
		$css_files = \App\Controller\Common::getCssFiles();
		$test = [];
		foreach ($css_files as $css_file) {
			$css = parse_url($css_file);
			if (!in_array($css['path'], $test) && \CString::pos($css['path'], 'tablet.css') === false && \CString::pos($css['path'], 'mobile.css') === false) {
				$minifier->add(APP_DIR_PATH . '/public' . $css['path']);
				$test[] = $css['path'];
			}
		}
		$minifier->minify(APP_DIR_PATH . '/public/css/styles.min.css');

//JS
		$minifier = new \MatthiasMullie\Minify\JS();
		$js_files = \App\Controller\Common::getJsFiles();

		$test = [];
		foreach ($js_files as $js_file) {
			$js = parse_url($js_file);
			if (!in_array($js['path'], $test) && $js['path'] !== '/2.1/') {
				$minifier->add(APP_DIR_PATH . '/public' . $js['path']);
				$test[] = $js['path'];
			}
		}
		$minifier->minify(APP_DIR_PATH . '/public/js/js.min.js');

		die('ok');
	}

}
