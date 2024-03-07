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
    <meta name="theme-name" content="telemagic">
    

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?= html()->cssFile('/css/jquery.autosuggest.css') ?>
    <?= html()->cssFile('/css/jquery-ui-1.10.4.custom.min.css', 'screen') ?>
    <? /* <!--[if lt IE 9]>
      <script src="dist/html5shiv.js"></script>
      <![endif]--> */ ?>
    <?
    /*$files = App\Controller\Common::getCssFiles();
    foreach ($files as $file) {
        echo html()->cssFile($file);
    }*/
    echo html()->cssFile('/css3/main.min.css?v=' . RESOURCE_UPDATE_TIME, 'screen');
    echo html()->cssFile('/css3/material.css', 'screen');
    echo html()->cssFile('/css3/modal.css?v=' . RESOURCE_UPDATE_TIME, 'screen');
    echo html()->cssFile('/css3/cart.css?v=' . RESOURCE_UPDATE_TIME, 'screen');
    ?>

    <?= app()->metadata()->renderCss() ?>

    <? if (app()->location()->currentId() != '76004' && ($_SERVER['REMOTE_ADDR'] != '93.158.228.86' || $_SERVER['REMOTE_ADDR'] != '93.181.225.108')) { ?>
        <meta name="verify-admitad" content="0b6a4d04de" />
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({
                google_ad_client: "ca-pub-8903618569646768",
                enable_page_level_ads: true
            });
        </script>
    <? } ?>	
    <script async src="//www.google.com/recaptcha/api.js"></script> 
</head>