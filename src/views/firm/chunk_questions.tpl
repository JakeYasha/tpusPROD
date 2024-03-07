<?if($items) {?>
<div class="qanda">
	<h2>Вопросы и ответы</h2>
	<span><a target="_blank" href="/page/away/?url=<?=encode('http://www.727373.ru')?>" rel="nofollow">с сайта</a></span>
	<?  foreach ($items as $q_id => $question) {?>
	<p><?=$question['text']?></p>
	<a target="_blank" href="/page/away/?url=<?=encode('http://www.727373.ru/question/'.$q_id.'/'.$question['mnemonick'].'.htm')?>" rel="nofollow">Посмотреть ответ</a>
	<?}?>
</div>
<?}?>