<div class="popup region-selection-block-wrapper">
	<div class="top_field">
		<div class="title">Выберите город</div>
	</div>
	<div class="region-selection-block">
		<?=$autocomplete?>
		<ul class="main-cities">
			<?  foreach ($cities as $city) {if(!$city['id'])continue;?>
			<li><a href="#" class="js-search-mode-selector-link js-action" data-id="<?=$city['id']?>" data-label="<?=$city['name']?>"><?=$city['name']?></a></li>
			<?}?>
		</ul>
	</div>
</div>