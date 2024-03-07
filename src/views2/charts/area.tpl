<? $chart_id = CRandom::uniqueId();?>
<?app()->metadata()->setJs(app()->chunk()->set(['items'=>$items,'title'=>$title,'chart_id'=>$chart_id])->render('charts.donut_data'))?>
<div id="charts_area_<?= $chart_id?>" style="width: <?=  isset($width) ? $width : '100%'?>; height: <?=  isset($height) ? $header : '500px'?>;<?=  isset($style) ? $style : ''?>"></div>
<div class="chart-png" id="charts_area_png_<?= $chart_id?>"></div>