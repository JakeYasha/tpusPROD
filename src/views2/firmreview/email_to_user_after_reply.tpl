<?
$firm = new \App\Model\Firm($item['id_firm']);
?><div style="background-color: #ffffff;" bgcolor="#ffffff">
    <p>На сайте TovaryPlus.ru появился ответ на Ваш отзыв о компании <strong>"<?=$firm->name()?>"</strong>.</p>
	<p></p>
	<p>Ваш отзыв:</p>
	<p><?=$item['text']?></p>
	<p></p>
	<p>Ваша оценка:</p>
	<p><?=$item['score']?></p>
	<p>Ответ компании <strong>"<?=$firm->name()?>"</strong></p>
	<p><?=$item['reply_text']?></p>
	<p></p>
	<p>Вы можете изменить свою оценку, перейдя по одной из соответствующих ссылок:</p>
	<ul>
		<li><a href="<?=APP_URL?>/firm-review/change-score/<?=$item['id']?>/5/?hash=<?=FirmReview::getChangeScoreHash($item['id'], $item['user_email'], 5)?>">Поставить "5"</a></li>
		<li><a href="<?=APP_URL?>/firm-review/change-score/<?=$item['id']?>/4/?hash=<?=FirmReview::getChangeScoreHash($item['id'], $item['user_email'], 5)?>">Поставить "4"</a></li>
		<li><a href="<?=APP_URL?>/firm-review/change-score/<?=$item['id']?>/3/?hash=<?=FirmReview::getChangeScoreHash($item['id'], $item['user_email'], 5)?>">Поставить "3"</a></li>
		<li><a href="<?=APP_URL?>/firm-review/change-score/<?=$item['id']?>/2/?hash=<?=FirmReview::getChangeScoreHash($item['id'], $item['user_email'], 5)?>">Поставить "2"</a></li>
		<li><a href="<?=APP_URL?>/firm-review/change-score/<?=$item['id']?>/1/?hash=<?=FirmReview::getChangeScoreHash($item['id'], $item['user_email'], 5)?>">Поставить "1"</a></li>
	</ul>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>