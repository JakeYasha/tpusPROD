<script src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['geochart']}]}"></script>
<script>
			google.setOnLoadCallback(drawRegionsMap);
			function drawRegionsMap() {
			var data = google.visualization.arrayToDataTable([
					['Region', 'Counter', 'Link'],
					['RU-YAR', 20, '/76/'],
					['RU-MOS', 50, '/77/']
			]);
					var options = {
					region: 'RU',
							colorAxis: {colors: ['#00853f', 'black', '#e31b23']},
							backgroundColor: '#ffffff',
							datalessRegionColor: '#f8bbd0',
							defaultColor: '#f5f5f5',
							resolution: 'provinces',
							displayMode: 'regions',
							region: 'RU',
							width: '100%',
							keepAspectRatio: false
					};
					var view = new google.visualization.DataView(data);
					view.setColumns([0, 1]);
					var chart = new google.visualization.GeoChart(document.getElementById('geochart-colors'));
					var pngHolder = document.getElementById('geochart-colors-png');
					google.visualization.events.addListener(chart, 'ready', function () {
						  pngHolder.innerHTML = '<img src="' + chart.getImageURI() + '">';
					});
					chart.draw(view, options);
			};
</script>
<div id="geochart-colors" style="width: 100%; height: 500px;"></div>
<div class="chart-png" id="geochart-colors-png"></div>
