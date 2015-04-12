<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <link href="{{ asset('/css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/master.css') }}" rel="stylesheet">
    <!-- Percentage Columns -->
    <link href="{{ asset('/css/percent-no-padding.css') }}" type="text/css" rel="stylesheet" media="only screen and (min-width: 0px)">
	<script src="{{ asset('/js/jquery-2.1.3.min.js') }}"></script>
	<script src="{{ asset('/js/moment.min.js') }}"></script>
	<script src="{{ asset('/js/moment-timezone-with-data.min.js') }}"></script>
	<script src="{{ asset('/js/highcharts.js') }}"></script>
	<script src="{{ asset('/js/twitchstats.js') }}"></script>
</head>
<body>
<div style="text-align:center;">
    <h1>@yield('title')</h1>
    <h3>Under Construction</h3>
</div>
<br />
@yield('content')
</body>
</html>