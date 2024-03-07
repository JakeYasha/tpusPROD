<? if ($pagination->getTotalPages() && $pagination->hasElems()) {?>
	<ul class="list pagination">
        <?= implode('', $pagination->getElems())?>
	</ul>
<?
}?>