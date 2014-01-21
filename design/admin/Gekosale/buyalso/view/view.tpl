{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/daterangepicker/css/ui.daterangepicker.css?v={{ appVersion }}" type="text/css"/>
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/daterangepicker/css/redmond/jquery-ui-1.7.1.custom.css?v={{ appVersion }}" type="text/css"/>
{% endblock %}	
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/daterangepicker/js/daterangepicker.jQuery.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1', {'packages':['corechart']});
	google.setOnLoadCallback(function(){
		$('#desktop-simple-stats-products-chart').GChart({
			fSource:  '{{ URL }}buyalso/confirm/{{ id }}',
			sType: 'pie',	
			oParams: {
				width: 1160,
				height: 600,
				chartArea: {
		        	width: 900,
		        	height: 520,
		        },
			}
		});
	});
</script>
{% endblock %}	
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/desktop.png" alt=""/>{% trans %}TXT_BUY_ALSO_LIST{% endtrans %}: {{ name }}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}buyalso" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
</ul>
<div class="block" id="desktop">
	<div class="simple-stats layout-two-columns">
				<div id="desktop-simple-stats-sales">
					<div class="chart" id="desktop-simple-stats-products-chart"></div>
				</div>
	</div>
</div>
{% endblock %}