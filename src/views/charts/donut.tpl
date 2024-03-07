<?if(count($items) > 1){?>
<? $chart_id = CRandom::uniqueId();?>
<?app()->metadata()->setJs(app()->chunk()->set(['items'=>$items,'title'=>$title,'chart_id'=>$chart_id])->render('charts.donut_data'))?>
<style type="text/css">#charts_donut_<?= $chart_id?> g g g rect, #charts_donut_<?= $chart_id?> g g g text {cursor: pointer;}</style>
<div class="charts_donut chart-holder" id="charts_donut_<?= $chart_id?>" style="width: <?=  isset($width) ? $width : '100%'?>; height: <?=  isset($height) ? $header : '500px'?>;<?=  isset($style) ? $style : ''?>"></div>
<div class="chart-png" id="charts_donut_png_<?= $chart_id?>"></div>
<?}?>