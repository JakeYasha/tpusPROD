<? if ($elems && (count($elems) > 1)) {?>
<?/*
<div class="breadcrumbs_list">
    <ul>
		<?foreach ($elems as $elem) {?>
		<?}?>
		<li class="cms_tree_first"><a href="">Каталог товаров и услуг</a>
            <ul>
                <li>
                    <a href="">Осветительные приборы</a>
                    <ul>
                        <li><a href="">Лампы</a></li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>*/?>
<div class="breadcrumbs inside">
    <ul>
        <?$i = 0;$_i = count($elems);foreach ($elems as $elem) {$i++;?>
		<li><?if($i===$_i){?><?= $elem['label']?><?} else {?><?= $bread_crumbs->renderElem($elem)?><?}?></li>
		<?}?>
    </ul>
</div>
<? }?>
