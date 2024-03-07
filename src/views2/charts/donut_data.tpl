google.setOnLoadCallback(drawChart<?=$chart_id?>);
function drawChart<?=$chart_id?>() {
	var data = google.visualization.arrayToDataTable([
		['Key', 'Val', 'Link'],
<? foreach ($items as $k => $v) {?>
			['<?= trim($k)?>', <?= $v['count']?>, '<?= $v['link']?>'],
<? }?>
	]);

	var view = new google.visualization.DataView(data);
	view.setColumns([0, 1]);

	var options = {
		title: '<?= $title?>',
		pieHole: 0.4,
		chartArea: {width: '80%'}
	};

	var chart = new google.visualization.PieChart(document.getElementById('charts_donut_<?= $chart_id?>'));
	var pngHolder = document.getElementById('charts_donut_png_<?= $chart_id?>');
	google.visualization.events.addListener(chart, 'ready', function () {
		  pngHolder.innerHTML = '<img src="' + chart.getImageURI() + '">';
	});
	chart.draw(view, options);
	console.log(chart.getImageURI());

	var selectHandler = function(e) {
		window.location = data.getValue(chart.getSelection()[0]['row'], 2);
	}

	// Add our selection handler.
	google.visualization.events.addListener(chart, 'select', selectHandler);
}