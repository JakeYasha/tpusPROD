<?if($images){?>
        <ul>
                <?foreach($images as $img) {?>
                        <li class="search_result_cell"><div class="image"><a title="Выбрать" href="/firm-user/ajax/set-price-image/?id_image=<?=$img->id()?>&id_price=<?=$id_price?>" class="js-action js-add-image-to-price button"><img style="width: " src="<?=$img->path()?>" /></a></div></li>
                <?}?>
        </ul>
<?}?>