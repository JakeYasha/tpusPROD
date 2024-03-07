<?= $bread_crumbs?>
<div class="black-block">Рекламные модули</div>
<?if($has_add_btn){?>
	<div class="search_result">
		<div class="attention-info">
			<p>Рекламные модули могут включать в себя информацию о специальных предложениях, акциях и скидках. </p>
		</div>
	</div>	
	<a href="/firm-user/advert-module/?mode=add" class="default-red-btn" style="margin-left: 20px;">Добавить модуль</a>
<?}?>
<div class="search_result">
	<?=$content?>
</div>