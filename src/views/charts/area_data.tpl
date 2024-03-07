google.setOnLoadCallback(drawChart);
function drawChart() {
var data = google.visualization.arrayToDataTable([
['Year', 'Sales', 'Expenses'],
['2013', 1000, 400],
['2014', 1170, 460],
['2015', 660, 1120],
['2016', 1030, 540]
]);

var options = {
title: 'Company Performance',
hAxis: {title: 'Year', titleTextStyle: {color: '#333'}},
vAxis: {minValue: 0}
};

var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
var pngHolder = document.getElementById('charts_area_png_<?= $chart_id?>');
google.visualization.events.addListener(chart, 'ready', function () {
	  pngHolder.innerHTML = '<img src="' + chart.getImageURI() + '">';
});
chart.draw(data, options);
}