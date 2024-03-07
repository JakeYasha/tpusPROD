<div class="b-form">
	<form<?=html()->renderAttrs($attrs)?>>
<?
foreach ($structure as $_elem) {
	if (isset($_elem['type'])) {
		switch ($_elem['type']) {
			case 'field':
				if ($_elem['_elem']['type'] == 'hidden_field') {
?>
		<?=$_elem['_elem']['html']?>
<?				} else {?>
		<div class="<?if ($_elem['_elem']['inline']) {?>b-form-elem-inline<?} else {?>b-form-elem<?}?>">
<?					if (!$_elem['_elem']['inline']) {?>
			<div class="label"><label><?=$_elem['_elem']['label']?></label>:</div>
<?					}?>
			<div class="elem-container">
<?					if ($_elem['_elem']['hint_before']) {?>
				<div class="hint-before"><?=$_elem['_elem']['hint_before']?></div>
<?					}?>
				<div class="elem"><?=$_elem['_elem']['html']?></div>
<?					if ($_elem['_elem']['hint_after']) {?>
				<div class="hint-after"><?=$_elem['_elem']['hint_after']?></div>
<?					}?>
			</div>
		</div>
<?
				}
				break;
			case 'few_fields':
?>
		<div class="<?if ($_elem['_elem']['inline']) {?>b-form-few-elems-inline<?} else {?>b-form-few-elems<?}?>">
<?				if (!$_elem['_elem']['inline']) {?>
			<div class="label"><label><?=$_elem['_elem']['label']?></label>:</div>
<?				}?>
			<div class="elems-container">
<?				if ($_elem['_elem']['hint_before']) {?>
				<div class="hint-before"><?=$_elem['_elem']['hint_before']?></div>
<?				}?>
				<div class="elems">
<?				foreach ($_elem['_elems'] as $_structure_sub_elem) { if ($_structure_sub_elem['type'] == 'hidden_field') {?>
					<?=$_structure_sub_elem['html']?>
<?				} else {?>
					<div class="elem"><?=$_structure_sub_elem['html']?></div>
<?				} }?>
				</div>
<?				if ($_elem['_elem']['hint_after']) {?>
				<div class="hint-after"><?=$_elem['_elem']['hint_after']?></div>
<?				}?>
			</div>
		</div>
<?
				break;
			case 'label':
?>
		<div class="b-form-label"><label><?=$_elem['_elem']['text']?>:</label></div>
<?
				break;
		}
	}
}
if (!empty($controls)) {
	$_elem_creator = new CInterfaceElemCreator();
?>
		<div class="b-form-control">
			<ul>
<?	foreach ($controls as $_control => $_control_props) {?>
				<li><?=$_elem_creator->renderElem('__' . $_control, $_control_props)?></li>
<?	}?>
			</ul>
		</div>
<?}?>
	</form>
</div>
