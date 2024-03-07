<?  foreach ($items as $item) {?>
<?if($item->exists()){?><span class="age-limit"><?=$item->name()?></span><?}?>
<?}?>