<? /* @var $firm Firm */?>
<div class="popup">
	<form<?= html()->renderAttrs($attrs)?>>
		<div class="top_field">
			<div class="title"><?= $heading?></div>
		</div>
		<div class="inputs">
			<input type="hidden" name="id_firm" value="<?=$firm->id()?>" />
			<label class="js-score-label">Ваша оценка фирмы <?=$firm->name()?> по 5 бальной системе</label>
			<div class="top_review">
				<div class="rate-big js-rate-links">
					<a href="#" data-index="1"></a><a href="#" data-index="2"></a><a href="#" data-index="3"></a><a href="#" data-index="4"></a><a href="#" data-index="5"></a>
				</div>
				<span class="result js-rate-preview"></span>
				<input type="hidden" name="score" value="" class="js-rate-score" />
			</div>
			<? foreach ($fields as $field) {?>
				<label><?= $field['label']?><?= $field['html']?></label>
			<? }?>
		</div>
		<div class="inputs"><label><a href="/page/show/pravila-publikacii-otzyvov.htm" target="_popup">Правила написания и публикации отзывов</a></label></div>
		<?= app()->capcha()->render()?>
		<div class="error-submit" style="height: 40px;"></div>
		<?= $controls['submit']['html']?>
	</form>
</div>