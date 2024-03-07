<?if($items){?>
<div class="new_company">
	<a class="head_h2" href="<?=app()->link('/firm/new/')?>">Новые компании</a>
	<div class="companies">
	<br />
	<?  foreach ($items as $it){?>
		<a href="<?=$it->link()?>" class="company_name"><?=$it->name()?></a>
	<?}?>
	</div>
</div>
<?}?>