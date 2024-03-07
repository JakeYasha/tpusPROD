<a href="#" onclick="window.open('/print/?print_url=<?=app()->request()->getRequestUri()?>', 'print', 'menubar=yes,location=no,resizable=yes,scrollbars=yes,status=yes')" class="print-button">Версия для печати</a>
<?=$breadcrumbs?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page">
		<?=str()->replace($item->val('text'), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()])?>
		<?if($item->val('show_sub_page')){?>
		<div class="for_clients_list">
		<h2>Дополнительно</h2>
		<ul>
		<?foreach ($childs as $child) {?>
			<li><a href="<?=$child->link()?>"><?=$child->val('name')?></a></li>
		<?}?>
		</ul>
		</div>
		<?}?>
	</div>
</div>