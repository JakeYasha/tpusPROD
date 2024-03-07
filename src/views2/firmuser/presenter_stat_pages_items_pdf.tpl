<div class="delimiter-block"></div>
<? $main_stat=false; $additional_stat=false; foreach ($items as $item) { 
        if ($item['stat_group'] == 'main_stat') $main_stat = true;
        if ($item['stat_group'] == 'additional_stat') $additional_stat=true;
    
} ?>
<?if ($additional_stat){?>
        <h2>Просмотренные страницы со списками фирм в рубриках каталогов *</h2>
        <table class="default-table pages-table">
                <tr>
                        <th>Страница</th>
                        <th class="last">Просмотры</th>
                </tr>
        <? foreach ($items as $item) { if ($item['stat_group'] == 'additional_stat') { ?>
                <tr>
                        <td><a href="<?=$item['url']?>"><?=$item['name']?></a></td>
                        <td class="last"><?=ceil($item['count']*STAT_ADD_COUNT)?></td>
                </tr>
        <?}}?>
        </table>
        <div class="delimiter-block"></div>
		<div class="attention-info" style="font-size: 0.85em;">
			<p>* В отчете "Просмотренные страницы со списками фирм в рубриках каталогов" представлено сколько раз за отчетный период и какие страницы каталогов были показаны посетителям сайта TovaryPlus.ru, где было упоминание Вашей фирмы или ее товаров и услуг.</p>        
		</div>

<?}?>
<?if ($main_stat){?>
        <h2>Просмотренные персональные страницы фирмы *</h2>
        <table class="default-table pages-table">
                <tr>
                        <th>Страница</th>
                        <th class="last">Просмотры</th>
                </tr>
        <? foreach ($items as $item) { if ($item['stat_group'] == 'main_stat') { ?>
                <tr>
                        <td><a href="<?=$item['url']?>"><?=$item['name']?></a></td>
                        <td class="last"><?=ceil($item['count']*STAT_ADD_COUNT)?></td>
                </tr>
        <?}}?>
        </table>
        <div class="delimiter-block"></div>
		<div class="attention-info" style="font-size: 0.85em;">
			<p>* В отчете "Просмотренные персональные страницы фирмы" представлено сколько раз за отчетный период и какие страницы были показаны посетителям сайта TovaryPlus.ru, где основное содержание страницы посвящено описанию Вашей фирмы или ее товаров и услуг.</p>        
		</div>

<?}?>
