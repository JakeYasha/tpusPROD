<?= $bread_crumbs?>
<?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
?>
<div class="cat_description">
	<h1><?=$title?></h1>
	<p>В этом разделе представлен каталог товаров Ярославля. Все предложения распределены по категориям и отражают информацию, размещенную на текущий момент в базе данных сайта. </p>
</div>
<? /* = $tabs */?>
<? if ($mode === 'catalog') {?>
	<div class="clearfix">
		<div class="for_clients_text_c clearfix firm-catalog">
			<? if ($items) {?>
				<div class="firm-catalog-list left">
					<ul>
						<?
						$j = 0;
						$half = ceil(count($items) / 2);
						foreach ($items as $mtKey => $arr) {
							$j++;
							$mtKey = $arr['mtkey'];
							?>
							<li><a href="<?= app()->link('/firm/bytype/' . $mtKey . '/')?>"><?= $main_types[$mtKey]['name']?></a>
								<ul>
							<? $i = 0;
							foreach ($arr['childs'] as $child) {
								$i++;
								if ($i == 6) break;?>
										<li><a href="<?= app()->link('/firm/bytype/' . $mtKey . '/' . $child['id'] . '/')?>"><?= $child['name']?></a></li>
							<? }?>
								</ul>
							</li>
			<? if ($j == $half) {?>
							</ul>
						</div>
						<div class="firm-catalog-list right">
							<ul>
			<? }?>
		<? }?>
					</ul>
				</div>			
	<? } else {?>
				<div class="cat_description"><p>К сожалению, на данный момент, мы не располагаем структурированным каталогом товаров и услуг для этого города.</p></div>
				<? }?>
		</div>
	</div>
<? } else {?>
	<div class="clearfix">
		<div class="for_clients_text_c clearfix firm-catalog firm-catalog-list alphabet">
			<ul>
	<? foreach ($alphabet as $a) {
		$a = $a['name'];?>
					<li><a href="<?= app()->link('/firm/catalog/alphabet/' . encode($a) . '/')?>"><?= $a?></a></li>
	<? }?>
			</ul>
		</div>
	</div>
<? }?>
<?
/*


  <div class="location">
  <a href="#" onclick="window.open('/print?<?= time()?>', 'print', 'menubar=yes,location=no,resizable=yes,scrollbars=yes,status=yes')" class="print">Версия для печати</a>
  <a href="/">Главная страница</a> - <?= $h1?>
  </div>
  <div class="delimLeft"><div class="delimRight"></div></div>
  <link type="text/css" rel="stylesheet" href="/css/lister.css" />
  <h1><?= $h1?></h1>
  <? if (!isset($byalphabet)) {?>
  <p class="small grey">В разделе каталог фирм представлен сгруппированный по видам деятельности справочник фирм, предприятий, организаций и магазинов с указанием их адресов, телефонов и прайс-листов.
  <? } else {?>
  <p class="small grey">В разделе каталог фирм представлен сгруппированный по видам деятельности справочник фирм, предприятий, организаций и магазинов с указанием их адресов, телефонов и прайс-листов.
  <? }?>
  <div class="space10"></div>
  <ul class="tabs_head">
  <li class="<? if (!isset($byalphabet)) {?>a_tab<? } else {?>na_tab<? }?>"><? if (!isset($byalphabet)) {?><img src="/img/red_bookmark.png" class="tab_bookmark" alt="" /><span class="tab_label"><? } else {?><a href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/catalog/"><? }?>По типу<? if (!isset($byalphabet)) {?></span><? } else {?></a><? }?></li>
  <li class="<? if (isset($byalphabet)) {?>a_tab<? } else {?>na_tab<? }?>"><? if (isset($byalphabet)) {?><img src="/img/red_bookmark.png" class="tab_bookmark" alt="" /><span class="tab_label"><? } else {?><a href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/catalog/alphabet/"><? }?>По алфавиту<? if (isset($byalphabet)) {?></span><? } else {?></a><? }?></li>
  <li style="clear: both;"></li>
  </ul>
  <div class="space10"></div>


  <? if (!isset($byalphabet)) {?>

  <? if (!isset($out)) {?>
  <!--noindex--><p class="small grey">К сожалению, на данный момент, мы не располагаем структурированным каталогом фирм для этого города. Для поиска интересующей Вас фирмы вы можете воспользоваться разделом "Просмотр фирм по алфавиту" или системой поиска на нашем сайте.<!--/noindex-->
  <? } else {?>

  <div class="pageColumnsLeft">
  <ul class="lister">
  <?
  $j = 0;
  $half = round(count($out) / 2);
  foreach ($out as $mtKey => $arr) {
  $j++;
  $mtKey = $arr['mtkey'];
  ?>
  <li>
  <!--		<a class="pm" href="#" onclick="topperClick(this);return false;"></a> -->
  <!--		<a class="pmAct" href="#"></a> -->
  <a style="margin-left:15px;" href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/bytype/<?= $mtKey?>/0/"><b><?= $mainTypes[$mtKey]['type']?></b><!-- (<?= $typesCnt[$mtKey]?>)--></a>
  <!--	    <div style="display:none;margin-left:-20px;"> -->
  <div style="display:block;">
  <?
  $i = 0;
  foreach ($arr['childs'] as $child) {
  $i++;
  ?><? if ($i != 1) {?>, <? }?><a href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/bytype/<?= $mtKey?>/<?= $child['id']?>/"><?= $child['type']?></a><? if ($i == 5) {?> ...<?
  break;
  }
  }
  ?>
  </div>
  </li>
  <? if ($j == $half) {?>
  </ul></div><div class="pageColumnsRight"><ul class="lister">
  <? }?>
  <? }?>
  </ul>
  </div>
  <? }?>
  <div class="clear"></div>
  <p class="small grey">В разделе "Просмотр фирм по типу" представлены только те фирмы, для которых определены виды деятельности по существующему рубрикатору. Полный список фирм, представленных на сайте, Вы можете посмотреть в разделе "Просмотр фирм по алфавиту" или воспользоваться поиском фирм на нашем сайте.<br /><br /></p>
  <? } else {?>
  <div class="letters">
  <?
  $skip = FALSE;
  foreach ($alphabet as $a) {
  $a = $a['name'];
  if (strlen($a) == 2 and ! $skip) {
  $skip = TRUE;
  ?></div><div class="letters" style="clear: left;"><? }?>
  <a<? if ($letter == $a) {?> class="act"<? }?> href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/letter/<?= $a?>/skip/"><?= $a?></a>
  <? if ($a == 'Z') {?><div class="clear"></div><? }?>
  <? }?>
  <div class="clear"></div>
  </div>
  <? if (!empty($subAlphabet)) {?>
  <div class="letters">
  <a href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/letter/<?= $letter?>/skip/"<? if (!$subLetter) {?> class="act"<? }?>>Все на <?= $letter?></a>
  <?
  foreach ($subAlphabet as $a) {
  $a = $a['name'];
  $b = HText::toLower(mb_substr($a, 1, 1, 'UTF-8'));
  ?>
  <a<? if ($subLetter == $b) {?> class="act"<? }?> href="<?= CPrice::getCurrentLocationId(TRUE)?>/firm/letter/<?= $letter?>/<?= $b?>/"><?= HText::likeSentance(HText::toLower($a))?></a>
  <? }?>
  <div class="clear"></div>
  </div>
  <? }?>
  <? }?>
  <? if (!empty($firms)) {?>
  <div class="space10"></div>
  <div class="pageColumnsLeft">
  <?
  $i = 0;
  $half = round(count($firms) / 2);
  foreach ($firms as $frm) {
  $i++;
  ?>
  <p><a href="/firm/show/<?= $frm['id_firm']?>/<?= $frm['id_service']?>/" class="red"><?= $frm['name']?></a> <?= HText::firstLetterUp(HText::toLower($cities[$frm['id_city']]['name']))?></p>
  <? if ($i == $half) {?>
  </div><div class="pageColumnsRight">
  <? }?>
  <? }?>
  </div>
  <? }?>
  <? if (substr(CPrice::getCurrentLocationId(TRUE), 0, 3) == '/76') {?>
  <div class="message">Если вы хотите разместить информацию о вашей фирме или предприятии на сайте - <a class="red" href="/applications/">заполните заявку</a></div>
  <? }?>
  <div class="clear"></div>
  <div class="direct">
  <!-- Яндекс.Директ -->
  <div class="delimLeft"><div class="delimRight"></div></div>
  <script src="/js/direct.js"></script></div> */?>
  
<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
?>