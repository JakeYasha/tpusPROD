<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Здравствуйте!<p>
        
	<p>На сайте tovaryplus.ru  <?=$params['mode']?> информация о филиале: <?=$params['firm_branch']->name()?>. Просим проверить информацию и внести при необходимости изменения в базе данных.</p>
	<p></p>
	<p>Для фирмы <a href="http://www.tovaryplus.ru<?=$params['firm']->link()?>"><?=$params['firm']->name()?></a><br />Адрес филиала: <?=$params['firm_branch']->address()?></p>
	<p> </p>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
    <br/>
</div>