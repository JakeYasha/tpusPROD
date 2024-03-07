<?if($items) {?>
<div class="qanda">
	<h2>Вопросы и ответы</h2>
	<span><a target="_blank" href="/page/away/?url=<?=encode('http://www.727373.ru')?>" rel="nofollow">с сайта 727373.ru</a></span>
	<div class="tp-qanda">
        <?  foreach ($items as $q_id => $question) {?>
        <div class="tp-col-4" style="margin-bottom: 10px;">
            <span><?=$question['text']?></span>
            <a target="_blank" href="/page/away/?url=<?=encode('http://www.727373.ru/question/'.$q_id.'/'.$question['mnemonick'].'.htm')?>" rel="nofollow" class="btn btn_primary btn_comments js-open-modal-ajax js-open-initialized" style="height: auto;width: auto;padding: 5px 15px;">Посмотреть ответ</a>
        </div>
        <?}?>
    </div>
</div>
<?}?>