google.setOnLoadCallback(drawBackgroundColor);

function drawBackgroundColor() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'X');
<? foreach ($items['columns'] as $col) {?>
	data.addColumn('number', '<?= $col?>');
<? }?>


data.addRows([
<? foreach ($items['data'] as $k=>$val) {?>['<?=$k?>',<?= implode(',', $val)?>],<? }?>
]);

var options = {
hAxis: {
title: ''
},
vAxis: {
title: '',
},
curveType: 'function',
backgroundColor: '#f1f8e9'
};

var chart = new google.visualization.LineChart(document.getElementById('charts_line_<?= $chart_id?>'));
var pngHolder = document.getElementById('charts_line_png_<?= $chart_id?>');
google.visualization.events.addListener(chart, 'ready', function () {
      pngHolder.innerHTML = '<img src="' + chart.getImageURI() + '">';
});
chart.draw(data, options);
}