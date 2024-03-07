<?= $bread_crumbs?>
<div class="for_clients clearfix guest-book">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<h1><?= $item->name()?></h1>
		<?=$item->val('text')?>
		<h2 class="black_border_bottom">Вакансии</h2>
		
		<?if(count($items)){  
			foreach ($items as $item) {?>
				<h3><?=$item->name()?></h3>
				<div class="notice-grey">
					<?if($item->val('responsibility')){?><h4>Обязанности</h4>
						<?=$item->val('responsibility')?><?}?>
					<?if($item->val('requirements')){?><h4>Требования</h4>
						<?=$item->val('requirements')?><?}?>
					<?if($item->val('terms')){?><h4>Условия</h4>
						<?=$item->val('terms')?><?}?>
					<?if($item->val('desirable')){?><h4>Желательно</h4>
						<p><?=$item->val('desirable')?></p><?}?>
					<?if($item->val('contact')){?><h4>Контактная информация</h4>
						<p><?=$item->val('contact')?></p><?}?>
				</div>	
			<? }
		} else {?>
			<p><br />На текущий момент в компании нет открытых вакансий.</p>
		<?}?>

		<h2 class="black_border_bottom">Вакансии других организаций размещенные на сайте</h2>
	    <p><br />Мы предлагаем Вам также посмотреть другие вакансии от организаций и предприятий Ярославля размещающих свою информацию на нашем сайте <a href="/76004/catalog/44/386/45969/vakansii-predlozheniya-raboty.htm?mode=price" class="red">Вакансии в Ярославле</a>, а также перечень <a href="/76004/firm/bytype/418/309/" class="red">Кадровых агентств Ярославля</a> которые могут помочь Вам в поиске работы.</p>
	</div>
</div>