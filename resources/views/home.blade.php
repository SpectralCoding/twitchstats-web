@extends('app')

@section('title', 'Twitch Stats')

@section('content')
	<div class="p1" id="mainchart"></div>
	<br />
	<div class="p3" id="top_three">
		<div class="one" id="top_emotes"></div>
		<div class="two" id="top_channels"></div>
		<div class="three" id="top_kappa"></div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			window.defaultTime = '10080';
			window.detaultTimeText = 'Last 7 Days';
			window.twitchCharts = new Array();
			Highcharts.setOptions({
				global: { useUTC: false },
				lang: { thousandsSep: ',' }
			});
			window.twitchCharts['mainchart'] = new Highcharts.Chart({
				chart: { type: 'line', renderTo: 'mainchart', animation: Highcharts.svg },
				title: { text: 'Global Chat Activity - ' + detaultTimeText },
				exporting: { enabled: false },
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					gridLineWidth: 1,
					dateTimeLabelFormats: { day: '%b %e' }
				},
				yAxis: {
					title: { text: 'Lines' },
					min: 0
				},
				legend: { enabled: false },
				tooltip: { valueSuffix: ' Lines' },
				series: [ { name: 'Total Lines' } ]
			});
			$.get('/channel/_global/' + window.defaultTime + '/true', function (jsonData) {
				var total = 0;
				var dataArr = [];
				$.each(jsonData['data'], function(timeID, lineCount) {
					dataArr.push([timeid_to_time(timeID, jsonData['accuracy']), lineCount]);
					total += lineCount;
				});
				window.twitchCharts['mainchart'].series[0].setData(dataArr);
				window.twitchCharts['mainchart'].setTitle(null, { text: "Total Lines: " + total.toLocaleString() });
			});
			window.twitchCharts['top_emotes'] = new Highcharts.Chart({
				chart: {
					type: 'pie',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					renderTo: 'top_emotes',
					animation: Highcharts.svg
				},
				title: { text: 'Top Emotes - ' + detaultTimeText },
				exporting: { enabled: false },
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Total Times Said'
				}]
			});
			$.get('/topemotes/_global/' + window.defaultTime + '/10', function (jsonData) {
				var total = 0;
				var dataArr = [];
				$.each(jsonData, function(emote, saidCount) {
					dataArr.push([emote, saidCount]);
					total += saidCount;
				});
				window.twitchCharts['top_emotes'].series[0].setData(dataArr);
				window.twitchCharts['top_emotes'].setTitle(null, { text: 'Total Emotes: ' + total.toLocaleString() });
			});
			window.twitchCharts['top_channels'] = new Highcharts.Chart({
				chart: {
					type: 'pie',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					renderTo: 'top_channels',
					animation: Highcharts.svg
				},
				title: { text: 'Popular Channels - ' + detaultTimeText },
				exporting: { enabled: false },
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Total Lines'
				}]
			});
			$.get('/topchannels/' + window.defaultTime + '/30', function (jsonData) {
				var total = 0;
				var dataArr = [];
				$.each(jsonData, function(channel, lineCount) {
					dataArr.push([channel, lineCount]);
					total += lineCount;
				});
				window.twitchCharts['top_channels'].series[0].setData(dataArr);
				window.twitchCharts['top_channels'].setTitle(null, { text: 'Total Lines: ' + total.toLocaleString()});
			});
			window.twitchCharts['top_kappa'] = new Highcharts.Chart({
				chart: {
					type: 'column',
					renderTo: 'top_kappa',
					animation: Highcharts.svg
				},
				title: { text: 'Kappa Leaders - ' + detaultTimeText },
				exporting: { enabled: false },
				xAxis: {
					type: 'category',
					labels: {
						rotation: -45,
						style: {
							fontSize: '13px',
							fontFamily: 'Verdana, sans-serif'
						}
					}
				},
				yAxis: {
					min: 0,
					minorTickInterval: 10,
					title: {
						text: 'Percentage'
					}
				},
				dataLabels: {
					enabled: true,
					rotation: -90,
					color: '#FFFFFF',
					align: 'right',
					format: '{point.y:.1f}', // one decimal
					y: 10, // 10 pixels down from the top
					style: {
						fontSize: '13px',
						fontFamily: 'Verdana, sans-serif'
					}
				},
				legend: { enabled: false },
				series: [{
					name: 'Channel'
				}]
			});
			$.get('/topchannelforemote/Kappa/' + window.defaultTime + '/10', function (jsonData) {
				var dataArr = [];
				$.each(jsonData, function(channel, emotePercent) {
					dataArr.push([channel, emotePercent]);
				});
				window.twitchCharts['top_kappa'].series[0].setData(dataArr);
				window.twitchCharts['top_kappa'].setTitle(null, { text: "Kappa as a Percent of Total Emotes" });
			});
		});
	</script>
@endsection
