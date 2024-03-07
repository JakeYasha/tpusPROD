<?=$bread_crumbs?>
<?if($has_banners){?>
<div class="black-block">Баннерная реклама</div>
<div class="search_result">
	<div class="search_result_top">
		<?=$tabs?>
	</div>
	<?if($dates_block){?>
	<ul class="date-block">
		<?foreach ($dates_block as $date){?>
		<li<?if($date['active']){?> class="active"<?}?>><a<?if($date['active']){?> class="js-action"<?}?> href="<?=$date['link']?>"><?=$date['name']?></a></li>
		<?}?>
	</ul>
	<?}?>
	<?=$items?>
	<?=$pagination?>
</div>
<?} else {?>
<div class="cat_description">
	<h1>У вас пока совсем нет баннеров :(</h1>
	<p>А ведь баннерная реклама является эффективным визуальным и интерактивным средством продвижения товаров и услуг, способствует повышению узнаваемости и престижа бренда, 
	а так же является удобным инструментом информирования о различных мероприятиях и акциях у компаний.</p>
	<p>На сайте tovaryplus.ru вы можете разместить графические баннеры (*.gif, *.jpeg, *.png), флэш-анимацию (*.swf) или текстовые баннеры с информацией о вашей компании и 
	продукции. </p>
</div>
<div class="black-block">Схема размещения баннеров на сайте tovaryplus.ru</div>
<div class="cat_description">
<p><img src="/uploaded/e/7/e7c495ae619f5d5bd767f7a2e41a9c66.png" alt="" width="280" style="margin-right: 10px;" /><img src="/uploaded/7/b/7b66938bd6c61038d49add93c78be874.png" width="280" height="316" style="margin-right: 10px;" /><img src="/uploaded/f/9/f9e703e22aa3ad68e03dff68f0a13572.png" alt="" width="280" /></p>
<p>&nbsp;</p>
<p>Для каждого баннера при размещении указывается определенный географический регион (область) для отображения. Показ баннера осуществляется только на страницах сайта связанных с выводом информации по выбранному региону.</p>
</div>
<table class="cltable">
<thead>
<tr><th colspan="2">Рекламное место</th><th>Размер, пикс.</th><th>Стоимость, руб./мес.</th><th>Примечание</th></tr>
</thead>
<tbody>
<tr>
<td style="text-align: left; background-color: #ffffff;" colspan="5"><strong>Графические баннеры</strong></td>
</tr>
<tr>
<td>1</td>
<td style="width: 165px;">Слайдер популярных&nbsp;рубрик</td>
<td>750х340</td>
<td>5000</td>
<td style="text-align: left;">
<p>Рекламное место показывается в блоке "популярные категории каталога" на следующих страницах сайта для <span>города (региона)</span>: главная страница, каталог товаров, каталог услуг, каталог оборудования и каталог фирм. Все баннеры слайдера&nbsp;размещаются одновременно на странице, смена слайдов происходит с интервалом в 5 секунд.</p>
<p>Максимальное количество слайдов для региона - 7.</p>
</td>
</tr>
<tr>
<td>2</td>
<td>Верхний баннер</td>
<td>500х130</td>
<td>7500</td>
<td style="text-align: left;">
<p>Сквозное рекламное место показывается на всех страницах сайта, за исключением главной страницы для города или региона.&nbsp;Баннеры показываются&nbsp;в ротации. Одновременно на рекламном месте показывается 2 баннера.</p>
<p>Максимальное количество баннеров для региона - 10.</p>
</td>
</tr>
<tr>
<td>3</td>
<td>Контекстный&nbsp;баннер</td>
<td>500х130</td>
<td>3500</td>
<td style="text-align: left;">
<p>Рекламное место показывается на страницах рубрик каталога товаров и услуг, рубрик каталога фирм, карточек товаров и на поисковых страницах сайта. Баннеры показываются&nbsp;в ротации. <span>Одновременно на рекламном месте показывается 2 баннера.</span></p>
<p>Для каждого баннера указывается при размещении не более 3 рубрик (подгрупп) связанных с тематикой баннера и ряд ключевых слов. Баннер показывается на странице, если на странице отображается информация относящаяся к указанным рубрикам или когда в названии рубрики и ее дочерних подрубриках встречается ключевое слово.</p>
<p>Максимальное количество баннеров в определенной рубрике - 10.</p>
</td>
</tr>
<tr>
<td style="text-align: left; background-color: #ffffff;" colspan="5"><strong>Текстовые баннеры</strong></td>
</tr>
<tr>
<td>4</td>
<td>Контекстный текстовый блок</td>
<td>230х100</td>
<td>1500</td>
<td style="text-align: left;">
<p>Рекламное место отображается на страницах рубрик каталога товаров и услуг, рубрик каталога фирм, карточках товаров и на поисковых страницах сайта. <span>Баннеры показываются в ротации. О</span><span>дновременно на рекламном месте максимально&nbsp;может показываться 3 баннера. При отображении на мобильных устройствах количество отображаемых баннеров уменьшается в зависимости от ширины экрана устройства до 1.</span>&nbsp;Если на странице нет баннеров для отображения на 3 баннерном месте, то для данных страниц&nbsp;выводится дополнительный блок текстовых баннеров, который располагается вместо&nbsp;3 баннерного места.</p>
<p>Для каждого баннера указывается при размещении не более 3 рубрик (подгрупп) связанных с тематикой баннера и ряд ключевых слов. Баннер показывается на странице, если на странице отображается информация относящаяся к указанным рубрикам или когда в названии рубрики и ее дочерних подрубриках встречается ключевое слово.</p>
<p>Максимальное количество баннеров в определенной рубрике - 12.</p>
</td>
</tr>
</tbody>
</table>
<div class="black-block">Общие требования к баннерам</div>
<div class="cat_description">
<p><strong>Графическим:</strong></p>
<ul>
<li>формат изображения GIF, JPEG, PNG с размером файла не более 100 кб</li>
<li>размеры изображения должны соответствовать размеру выбранного баннера</li>
</ul>
<p><strong>Флеш:</strong></p>
<ul>
<li>для flash-баннеров, размещающихся на сайтах, адрес рекламируемого сайта или страницы баннер <strong>должен считывать из параметра </strong>(название параметра link_url)</li>
</ul>
<p><strong>Текстовым</strong>:</p>
<ul>
<li>заголовок не должен превышать 30 символов, включая пробелы и знаки препинания</li>
<li>текст объявления - не более 120 символов</li>
<li>контактная информация - не более 30 символов</li>
<li>ключевые слова - не более 120 символов</li>
<li>формат изображения GIF, JPEG, PNG размер 230*100 px</li>
</ul>
</div>
<?}?>