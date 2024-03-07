<head>
	<?= html()->charset('utf-8')?>
	<?= app()->metadata()->renderTitle()?>
	<?= app()->metadata()->renderMetatags()?>
	<?= app()->metadata()->renderLinks()?>
	<?= app()->metadata()->renderSearchLink()?>
	<?= app()->metadata()->renderFavicon()?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="/favicon.ico" rel="shortcut icon" />
	<?= html()->cssFile('/css/client.main.css?v='.time())?>
	<?= html()->cssFile('/css/client.extended.css?v='.time())?>
	<?= html()->cssFile('/css/mobile.css')?>
	<?= html()->cssFile('/css/jquery.fancybox.css')?>
        <?= html()->cssFile('/css/jquery.qtip.min.css')?>
        <?= html()->cssFile('/css/jquery.autosuggest.css')?>
	<?= html()->cssFile('/css/slick.css')?>
	<?= html()->cssFile('/css/jquery-ui-1.10.4.custom.min.css', 'screen')?>
	<?= html()->cssFile('/css/firm-user.css?v='.time())?>
	<?= app()->metadata()->renderCssFiles()?>
	<?= app()->metadata()->renderCss()?>
</head>