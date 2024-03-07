<?if($items){
//echo app()->request()->getRequestUri();
?>
    <div class="mdc-layout-grid__inner">
        <?  foreach ($items as $_item) {?>
        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
            <h5 class="footer__heading"><?=$_item['name']?></h5>
            <ul class="list">
                <?foreach ($_item['subitems'] as $__item){?>
                    <li class="list__item list__item_footer"><a href="<?=  str()->replace($__item['link'], '_L_', app()->location()->currentId())?>" class="list__link list__link_footer"><?=$__item['name']?></a></li>
                <?}?>
            </ul>
        </div>
        <?}?>
    </div>
<?}?>