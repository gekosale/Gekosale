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
		$('#desktop-simple-stats-customers-chart').GChart({
			fSource:  '{{ URL }}mainside/view/clients'
		});
		
	});

		$(document).ready(function(){
			 $('#period').daterangepicker({
				dateFormat : 'yy/mm/dd',
				posY : '258px',
				onChange: function(){
					location.hash = Base64.encode($('#period').val());
				}, 
			});	
		});
	   
    </script>
{% endblock %}	
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/desktop.png" alt=""/>Statystyki klientów</h2>
<div class="block" id="desktop">
	<div class="simple-stats layout-two-columns">
		<div class="column narrow">
			<dl class="stats-summary">
			    <dt>{% trans %}TXT_CLIENTS{% endtrans %} ({% trans %}TXT_TODAY{% endtrans %} / {% trans %}TXT_CURRENT_MONTH{% endtrans %})</dt><dd>{{ summaryStats.todayclients.totalclients }} / {{ summaryStats.summaryclients.totalclients }} </dd>
			</dl>
		</div>
		<div class="column wide">
			<div class="tabs">
				<ul>
					<li><a href="#desktop-simple-stats-customers">{% trans %}TXT_CLIENTS{% endtrans %}</a></li>
				</ul>
			</div>
			<div class="box">
				<div class="field-text" >
					<label for="desktop-simple-stats-orders-range" style="float: left;margin-top: 3px;margin-right: 5px;">{% trans %}TXT_PERIOD_LIST{% endtrans %}:</label>
					<span class="field" style="width: 150px;">
						<input type="text" id="period" class="period" style="width:142px" value="{{ from }} - {{ to }}" />
					</span>
				</div>
				<div id="desktop-simple-stats-customers">
					<div class="chart" id="desktop-simple-stats-customers-chart"></div>
				</div>
			</div>
		</div>
	</div>
</div>
{% endblock %}						


{% block sticky %}
{% include sticky %}
{% endblock %}