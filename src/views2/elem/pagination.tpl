<? if ($pagination->getTotalPages() && $pagination->hasElems()) {?>
	<div class="pagination">
		<ul>
	<?= implode('', $pagination->getElems())?>
		</ul>
	</div>
<?
}?>