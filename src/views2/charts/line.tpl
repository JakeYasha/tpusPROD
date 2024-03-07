<?if(isset($items['data']) && count($items['data']) > 1){?>
<? $chart_id = CRandom::uniqueId();?>
<?app()->metadata()->setJs(app()->chunk()->set(['items'=>$items,'title'=>$title,'chart_id'=>$chart_id])->render('charts.line_data'))?>
<div class="chart-holder" id="charts_line_<?= $chart_id?>" style="width: <?=  isset($width) ? $width : '100%'?>; height: <?=  isset($height) ? $header : '500px'?>;<?=  isset($style) ? $style : ''?>"></div>
<div class="chart-png" id="charts_line_png_<?= $chart_id?>"></div>
<?}?>