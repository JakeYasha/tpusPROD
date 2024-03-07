<head>
    <?= html()->charset('utf-8') ?>
    <?= app()->metadata()->renderHotlead() ?>
    <?= app()->metadata()->renderTitle() ?>
    <?= app()->metadata()->renderMetatags() ?>
    <?= app()->metadata()->renderLinks() ?>
    <?= app()->metadata()->renderSearchLink() ?>
    <?= app()->metadata()->renderFavicon() ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="apple-mobile-web-app-title" content="TovaryPlus">
    <meta name="application-name" content="TovaryPlus">
    <meta name="msapplication-config" content="/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <? /* <!--[if lt IE 9]>
      <script src="dist/html5shiv.js"></script>
      <![endif]--> */ ?>
    <?
    if (1) {//APP_IS_DEV_MODE) {
        $files = App\Controller\Common::getCssFiles();
        foreach ($files as $file) {
            echo html()->cssFile($file);
        }
		echo html()->cssFile('/css/mobile.css?v=' . RESOURCE_UPDATE_TIME, 'screen');
		echo html()->cssFile('/css/style.css?v=' . RESOURCE_UPDATE_TIME, 'screen');
		echo html()->cssFile('/css/tablet.css?v=' . RESOURCE_UPDATE_TIME, 'only screen and (min-width: 768px) and (max-width: 1023px)');
		echo html()->cssFile('/css/mobile2.css?v=' . RESOURCE_UPDATE_TIME, 'only screen and (min-width: 0) and (max-width: 767px)');
		
    } else {
        ?>
        <?= html()->cssFile('/css/styles.min.css?v=' . RESOURCE_UPDATE_TIME, 'screen') ?>
		<?
		echo html()->cssFile('/css/tablet.css?v=' . RESOURCE_UPDATE_TIME, 'only screen and (min-width: 768px) and (max-width: 1023px)');
		echo html()->cssFile('/css/mobile2.css?v=' . RESOURCE_UPDATE_TIME, 'only screen and (min-width: 0) and (max-width: 767px)');
		?>
	
    <? } ?>
<?= app()->metadata()->renderCss() ?>
<? if (app()->location()->currentId() != '76004' && ($_SERVER['REMOTE_ADDR'] != '93.158.228.86' || $_SERVER['REMOTE_ADDR'] != '93.181.225.108')) { ?>
        <!-- <meta name="verify-admitad" content="0b6a4d04de" />
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({
                google_ad_client: "ca-pub-8903618569646768",
                enable_page_level_ads: true
            });
        </script> -->
    <? } ?>	
    <script async src="//www.google.com/recaptcha/api.js"></script> 
    
</head>