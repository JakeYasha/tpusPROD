<? if ($groups) {?>
<div class="clearfix firm-catalog">
	<?foreach ($items as $id_group => $arr) {?>
		<h2><?=$groups[$id_group]['name']?></h2>
		<div class="tags_field">
			<ul>
				<?foreach ($arr as $id_subgroup => $child) {?>
					<li><a href="/firm/show/<?=$firm->id_firm()?>/<?=$firm->id_service()?>/?id_catalog=<?=$subgroups[$id_subgroup]['id'].'&mode=price'?>" rel="nofollow"><?=$subgroups[$id_subgroup]['name']?></a></li>
				<?}?>
			</ul>
			<? if (count($arr) > 6) {?>
				<div class="show_more">
					<div class="line"></div>
					<a href="#" class="js-show-more-tags"><span>Показать все</span></a>
				</div>
			<? }?>
		</div>
	<?}?>
</div>
<?}?>
