<?
/*
$item['user_name']
$item['user_email']
$item['user_phone']
$item['question']
$item['answer']
<?=('/consumer/show/'.$item['id'].'/')?>
 */
?>
<p><strong><?=$item['user_name']?>, Вы задали вопрос на сайте www.tovaryplus.ru в рубрике «Защита прав потребителей»</strong></p>
<p><?=$item['question']?></p>
<p>Вопрос задан: <?=$item['timestamp_inserting']?></p>
<p><strong>Мы получили ответ на Ваш вопрос:</strong></p>
<?=$item['answer']?><br/>
<a href="<?=('http://tovaryplus.ru/consumer/show/'.$item['id'].'/')?>" >Ссылка на ваш вопрос на сайте tovaryplus.ru</a>

<p>С уважением,<br/>
специалисты отдела по защите прав потребителей<br/>
Управления развития потребительского рынка, предпринимательства и туризма<br/>
мэрии города Ярославля
</p>