<?= $bread_crumbs?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page consumer">
		<h1><?= $item->val('metadata_title')?></h1>
		<h2 class="black_border_bottom">Вопрос</h2>
		<p class="description">От: <?=$item->val('user_name')?>,  <?=date("d.m.Y", CDateTime::toTimestamp($item->val('timestamp_inserting')))?></p>
		<?= $item->val('question')?>
		<h2 class="black_border_bottom">Ответ</h2>
		<p class="description">Отвечает: <?=$item->val('specialist_name')?>, <?=date("d.m.Y", CDateTime::toTimestamp($item->val('answer_timestamp')))?></p>
		<p></p>
		<div class="notice-blue">
			<?=$item->val('answer')?>
		</div>
		<div class="all_block">
			<p>Вы можете <strong>задать свой вопрос</strong> специалистам отдела по защите прав потребителей</p>
			<ul>
				<li><b>по телефонам</b> (4852) 40-49-37, 40-49-38, 40-49-39 в отдел по защите прав потребителей</li>
				<li><b>на «Горячую линию»</b> каждый последний четверг месяца с 16:00 по 17:00 по телефону (4852) 72-73-73.</li>
				<li><b>оставив вопрос на нашем сайте</b>, указав контактный адрес электронной почты, по которому вам будет выслан ответ на ваш вопрос. Подготовка ответа и его публикация может составлять несколько дней, при срочных вопросах рекомендуем звонить непосредственно в отдел по защите прав потребителей.</li>
			</ul>
		</div>	
		<div class="all_block">
			<?=$form?>
		</div>
	</div>
</div>
<div class="pre_footer_adv_block">
	<?=app()->chunk()->render('adv.bottom_banners')?>
</div>