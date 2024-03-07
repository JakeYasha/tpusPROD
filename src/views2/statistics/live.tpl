<?= $breadcrumbs?>
<?=app()->chunk()->render('common.print_button')?>
<div class="for_clients clearfix">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<?= $text->val('text')?>
		<script src="/plugins/amcharts/amcharts/amcharts.js"></script>
		<?/*<script src="/plugins/amcharts/amcharts/serial.js"></script> */?>
		<script>
			var chartDayData = new Array(<?= $days?>);
			var chartMonthData = new Array(<?= $months?>);
			var dateChart, monthChart;

			AmCharts.ready(function () {
				// DAILY CHART
				dayChart = new AmCharts.AmSerialChart();
				dayChart.dataProvider = chartDayData;
				dayChart.categoryField = "date";
				dayChart.marginTop = 0;
				dayChart.export = {
					"enabled": true,
					"menu": []
				};
				//
				// DAILY AXES
				// category axis
				var categoryDayAxis = dayChart.categoryAxis;
				//categoryDayAxis.parseDates = true;
				categoryDayAxis.minPeriod = "DD";
				categoryDayAxis.autoGridCount = false;
				categoryDayAxis.gridCount = 50;
				categoryDayAxis.gridAlpha = 0;
				categoryDayAxis.gridColor = "#000000";
				categoryDayAxis.axisColor = "#555555";
				categoryDayAxis.labelFrequency = 2;
				categoryDayAxis.dateFormats = [{
						period: "DD",
						format: "DD"
					}, {
						period: "WW",
						format: "MMM DD"
					}, {
						period: "MM",
						format: "MMM"
					}, {
						period: "YYYY",
						format: "YYYY"
					}];
				// Integer value axis            
				var leftDayAxis = new AmCharts.ValueAxis();
				leftDayAxis.gridAlpha = 0.05;
				leftDayAxis.axisAlpha = 0;
				leftDayAxis.showLastLabel = false;
				dayChart.addValueAxis(leftDayAxis);
				// Time value axis 
				var timeDayAxis = new AmCharts.ValueAxis();
				timeDayAxis.position = "right";
				timeDayAxis.gridAlpha = 0;
				timeDayAxis.axisAlpha = 0.05;
				timeDayAxis.labelsEnabled = false;
				timeDayAxis.duration = "ss";
				timeDayAxis.durationUnits = {
					DD: "д. ",
					hh: "ч ",
					mm: "мин ",
					ss: "сек"
				};
				dayChart.addValueAxis(timeDayAxis);
				// END DAILY AXES
				//
				// DAILY GRAPHS
				// VisitTime graph
				var visitDayTimeGraph = new AmCharts.AmGraph();
				visitDayTimeGraph.title = "Время на сайте";
				visitDayTimeGraph.valueField = "visittime";
				visitDayTimeGraph.type = "column";
				visitDayTimeGraph.fillAlphas = 0.3;
				visitDayTimeGraph.cornerRadiusTop = 2;
				visitDayTimeGraph.lineThickness = 1;
				visitDayTimeGraph.valueAxis = timeDayAxis;
				visitDayTimeGraph.lineColor = "#999999";
				visitDayTimeGraph.balloonText = "[[value]]";
				visitDayTimeGraph.legendValueText = "[[value]]";
				dayChart.addGraph(visitDayTimeGraph);
				// Visits graph
				var visitsDayGraph = new AmCharts.AmGraph();
				visitsDayGraph.title = "Посещений";
				visitsDayGraph.valueField = "visits";
				visitsDayGraph.type = "line";
				visitsDayGraph.valueAxis = leftDayAxis;
				visitsDayGraph.lineColor = "#CC0000";
				visitsDayGraph.balloonText = "[[value]]";
				visitsDayGraph.lineThickness = 1;
				visitsDayGraph.legendValueText = "[[value]]";
				visitsDayGraph.bullet = "round";
				visitsDayGraph.bulletColor = "#ffffff";
				visitsDayGraph.bulletBorderColor = "#CC0000";
				visitsDayGraph.bulletBorderThickness = 1;
				visitsDayGraph.bulletSize = 6;
				dayChart.addGraph(visitsDayGraph);
				// PageViews graph
				var pageViewsDayGraph = new AmCharts.AmGraph();
				pageViewsDayGraph.title = "Просмотров";
				pageViewsDayGraph.valueField = "pageviews";
				pageViewsDayGraph.type = "line";
				pageViewsDayGraph.valueAxis = leftDayAxis;
				pageViewsDayGraph.lineColor = "#FF9900";
				pageViewsDayGraph.balloonText = "[[value]]";
				pageViewsDayGraph.legendValueText = "[[value]]";
				pageViewsDayGraph.bullet = "round";
				pageViewsDayGraph.bulletColor = "#ffffff";
				pageViewsDayGraph.bulletBorderColor = "#FF9900";
				pageViewsDayGraph.bulletBorderThickness = 1;
				pageViewsDayGraph.bulletSize = 6;
				dayChart.addGraph(pageViewsDayGraph);
				// Visitors graph
				var visitorsDayGraph = new AmCharts.AmGraph();
				visitorsDayGraph.title = "Посетителей";
				visitorsDayGraph.valueField = "visitors";
				visitorsDayGraph.type = "line";
				visitorsDayGraph.valueAxis = leftDayAxis;
				visitorsDayGraph.lineColor = "#000000";
				visitorsDayGraph.balloonText = "[[value]]";
				visitorsDayGraph.lineThickness = 1;
				visitorsDayGraph.legendValueText = "[[value]]";
				visitorsDayGraph.bullet = "round";
				visitorsDayGraph.bulletColor = "#ffffff";
				visitorsDayGraph.bulletBorderColor = "#000000";
				visitorsDayGraph.bulletBorderThickness = 1;
				visitorsDayGraph.bulletSize = 6;
				dayChart.addGraph(visitorsDayGraph);

				// CURSOR                
				var chartDayCursor = new AmCharts.ChartCursor();
				chartDayCursor.zoomable = false;
				chartDayCursor.categoryBalloonDateFormat = "DD";
				chartDayCursor.cursorAlpha = 0;
				dayChart.addChartCursor(chartDayCursor);

				// LEGEND
				var dayLegend = new AmCharts.AmLegend();
				dayLegend.bulletType = "round";
				dayLegend.equalWidths = false;
				dayLegend.valueWidth = 120;
				dayLegend.color = "#000000";
				dayChart.addLegend(dayLegend);

				// WRITE                                
				dayChart.write("daychartdiv")

				// MONTHLY CHART
				monthChart = new AmCharts.AmSerialChart();
				monthChart.dataProvider = chartMonthData;
				monthChart.categoryField = "date";
				monthChart.marginTop = 0;
				monthChart.export = {
					"enabled": true,
					"menu": []
				};
				// MONTHLY AXES
				// Category axis
				var categoryMonthAxis = monthChart.categoryAxis;
				//categoryMonthAxis.parseDates = true;
				categoryMonthAxis.minPeriod = "MM";
				categoryMonthAxis.autoGridCount = false;
				categoryMonthAxis.gridCount = 50;
				categoryMonthAxis.gridAlpha = 0;
				categoryMonthAxis.gridColor = "#000000";
				categoryMonthAxis.axisColor = "#555555";
				categoryMonthAxis.labelFrequency = 2;
				categoryMonthAxis.dateFormats = [{
						period: "DD",
						format: "DD"
					}, {
						period: "WW",
						format: "MMM DD"
					}, {
						period: "MM",
						format: "MMM"
					}, {
						period: "YYYY",
						format: "YYYY"
					}];
				// Integer value axis            
				var leftMonthAxis = new AmCharts.ValueAxis();
				leftMonthAxis.gridAlpha = 0.05;
				leftMonthAxis.axisAlpha = 0;
				leftMonthAxis.showLastLabel = false;
				monthChart.addValueAxis(leftMonthAxis);
				// Time value axis 
				var timeMonthAxis = new AmCharts.ValueAxis();
				timeMonthAxis.position = "right";
				timeMonthAxis.gridAlpha = 0;
				timeMonthAxis.axisAlpha = 0.05;
				timeMonthAxis.labelsEnabled = false;
				timeMonthAxis.duration = "ss";
				timeMonthAxis.durationUnits = {
					DD: "д. ",
					hh: "ч ",
					mm: "мин ",
					ss: "сек"
				};
				monthChart.addValueAxis(timeMonthAxis);
				// END MONTHLY AXES
				//
				// MONTHLY GRAPHS
				// VisitTime graph
				var visitMonthTimeGraph = new AmCharts.AmGraph();
				visitMonthTimeGraph.title = "Время на сайте";
				visitMonthTimeGraph.valueField = "visittime";
				visitMonthTimeGraph.type = "column";
				visitMonthTimeGraph.fillAlphas = 0.3;
				visitMonthTimeGraph.cornerRadiusTop = 2;
				visitMonthTimeGraph.lineThickness = 1;
				visitMonthTimeGraph.valueAxis = timeMonthAxis;
				visitMonthTimeGraph.lineColor = "#999999";
				visitMonthTimeGraph.balloonText = "[[value]]";
				visitMonthTimeGraph.legendValueText = "[[value]]";
				monthChart.addGraph(visitMonthTimeGraph);
				// Visits graph
				var visitsMonthGraph = new AmCharts.AmGraph();
				visitsMonthGraph.title = "Посещений";
				visitsMonthGraph.valueField = "visits";
				visitsMonthGraph.type = "line";
				visitsMonthGraph.valueAxis = leftMonthAxis;
				visitsMonthGraph.lineColor = "#CC0000";
				visitsMonthGraph.balloonText = "[[value]]";
				visitsMonthGraph.lineThickness = 1;
				visitsMonthGraph.legendValueText = "[[value]]";
				visitsMonthGraph.bullet = "round";
				visitsMonthGraph.bulletColor = "#ffffff";
				visitsMonthGraph.bulletBorderColor = "#CC0000";
				visitsMonthGraph.bulletBorderThickness = 1;
				visitsMonthGraph.bulletSize = 6;
				monthChart.addGraph(visitsMonthGraph);
				// PageViews graph
				var pageViewsMonthGraph = new AmCharts.AmGraph();
				pageViewsMonthGraph.title = "Просмотров";
				pageViewsMonthGraph.valueField = "pageviews";
				pageViewsMonthGraph.type = "line";
				pageViewsMonthGraph.valueAxis = leftMonthAxis;
				pageViewsMonthGraph.lineColor = "#FF9900";
				pageViewsMonthGraph.balloonText = "[[value]]";
				pageViewsMonthGraph.legendValueText = "[[value]]";
				pageViewsMonthGraph.bullet = "round";
				pageViewsMonthGraph.bulletColor = "#ffffff";
				pageViewsMonthGraph.bulletBorderColor = "#FF9900";
				pageViewsMonthGraph.bulletBorderThickness = 1;
				pageViewsMonthGraph.bulletSize = 6;
				monthChart.addGraph(pageViewsMonthGraph);
				// Visitors graph
				var visitorsMonthGraph = new AmCharts.AmGraph();
				visitorsMonthGraph.title = "Посетителей";
				visitorsMonthGraph.valueField = "visitors";
				visitorsMonthGraph.type = "line";
				visitorsMonthGraph.valueAxis = leftMonthAxis;
				visitorsMonthGraph.lineColor = "#000000";
				visitorsMonthGraph.balloonText = "[[value]]";
				visitorsMonthGraph.lineThickness = 1;
				visitorsMonthGraph.legendValueText = "[[value]]";
				visitorsMonthGraph.bullet = "round";
				visitorsMonthGraph.bulletColor = "#ffffff";
				visitorsMonthGraph.bulletBorderColor = "#000000";
				visitorsMonthGraph.bulletBorderThickness = 1;
				visitorsMonthGraph.bulletSize = 6;
				monthChart.addGraph(visitorsMonthGraph);
				// CURSOR                
				var chartMonthCursor = new AmCharts.ChartCursor();
				chartMonthCursor.zoomable = false;
				chartMonthCursor.categoryBalloonDateFormat = "DD";
				chartMonthCursor.cursorAlpha = 0;
				monthChart.addChartCursor(chartMonthCursor);

				// LEGEND
				var monthLegend = new AmCharts.AmLegend();
				monthLegend.bulletType = "round";
				monthLegend.equalWidths = false;
				monthLegend.valueWidth = 120;
				monthLegend.color = "#000000";
				monthChart.addLegend(monthLegend);

				// WRITE                                
				monthChart.write("monthchartdiv")
				$('svg').each(function (i, v) {
					$(v).children('g').eq(13).remove();
				});
			});
		</script>
		<div class="amchart-holder" id="daychartdiv" style="width:95%; height:400px;"></div>
		<div class="chart-png" id="daychartdiv_png"><img src="http://www.liveinternet.ru/stat/tovaryplus.ru/index.gif?total=yes;graph=yes" /></div>
		<noscript>
		<img src="http://www.liveinternet.ru/stat/tovaryplus.ru/index.gif?total=yes;graph=yes" alt="Посещаемость сайта за последние 30 дней" />
		</noscript>
		<p>&nbsp;</p>
		<div style="text-align: center;"><p class="red bold">Посещаемость сайта по месяцам</p></div>
		<div class="amchart-holder" id="monthchartdiv" style="width:95%; height:400px;"></div>
		<div class="chart-png" id="monthchartdiv_png"><img src="http://www.liveinternet.ru/stat/tovaryplus.ru/index.gif?period=month;total=yes;graph=yes" /></div>
		<noscript>
		<img src="http://www.liveinternet.ru/stat/tovaryplus.ru/index.gif?period=month;total=yes;graph=yes" alt="Посещаемость сайта по месяцам" />
		</noscript>
	</div>
</div>