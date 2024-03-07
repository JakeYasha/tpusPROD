<?if ($items) {?>
<menu>
	<ul>
<?	foreach ($items as $_item) {?>
		<li>
			<a<?if ($_item['is_active']) {?> class="active"<?}?> href="<?=\encode($_item['link'])?>"><?=\encode($_item['name'])?></a>
<?		if ($_item['subitems']) {?>
			<ul>
<?			foreach ($_item['subitems'] as $_subitem) {?>
				<li><a<?if ($_subitem['is_active']) {?> class="active"<?}?> href="<?=\encode($_subitem['link'])?>"><?=\encode($_subitem['name'])?></a></li>
<?			}?>
			</ul>
<?		}?>
		</li>
<?	}?>
	</ul>
</menu>
<?}?>
