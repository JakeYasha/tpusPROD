<?
$firm = new \App\Model\Firm($item['id_firm']);
?><div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>На сайте TovaryPlus.ru добавлен новый отзыв о компании <strong>"<?=$firm->name()?>"</strong>.</p>
	<p></p>
	<p><strong>Отзыв:</strong></p>
	<p><?=$item['text']?></p>
	<p></p>
	<p><strong>Оценка:</strong></p>
	<p><?=$item['score']?></p>
	<p></p>
        Для просмотра и управления всеми отзывами о компании - <a href="http://www.tovaryplus.ru/firm-manager/set-firm/<?=$item['id']?>/?redirect=/firm-user/review/">перейдите по ссылке в личный кабинет</a>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>