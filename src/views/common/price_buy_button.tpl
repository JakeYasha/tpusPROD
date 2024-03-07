<?if($item['is_yml']){?>
<a class="<?= isset($class) ? $class : 'buy'?><? if (isset($big_button)) {?> big<? }?>" href="<?=$item['link']?>" rel="nofollow">В магазин</a>
<?} else {?>
<a class="<?= isset($class) ? $class : 'buy'?><? if (isset($big_button)) {?> big<? }?> fancybox fancybox.ajax" href="/app-ajax/add-to-cart/?price_id=<?= $item['id']?>&count=1" rel="nofollow">В корзину</a>
<?}?>