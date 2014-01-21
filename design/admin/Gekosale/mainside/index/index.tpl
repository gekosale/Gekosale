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
		$('#desktop-simple-stats-sales-chart').GChart({
			fSource:  '{{ URL }}{{ CURRENT_CONTROLLER }}/view/sales'
		});
		
		$('#desktop-simple-stats-orders-chart').GChart({
			fSource:  '{{ URL }}{{ CURRENT_CONTROLLER }}/view/orders'
		});
		
		$('#desktop-simple-stats-customers-chart').GChart({
			fSource:  '{{ URL }}{{ CURRENT_CONTROLLER }}/view/clients'
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
<h2>{% trans %}TXT_DESKTOP{% endtrans %}</h2>
<div class="block" id="desktop">
	<div class="simple-stats layout-two-columns">
		<div class="column narrow">
			<dl class="stats-summary">
				<dt>{% trans %}TXT_SALES{% endtrans %} ({% trans %}TXT_TODAY{% endtrans %} / {% trans %}TXT_CURRENT_MONTH{% endtrans %})</dt><dd>{{ summaryStats.todaysales.total }} {% trans %}TXT_CURRENCY{% endtrans %} / {{ summaryStats.summarysales.total }} {% trans %}TXT_CURRENCY{% endtrans %}</dd>
				<dt>{% trans %}TXT_ORDERS{% endtrans %} ({% trans %}TXT_TODAY{% endtrans %} / {% trans %}TXT_CURRENT_MONTH{% endtrans %})</dt><dd>{{ summaryStats.todaysales.orders }} / {{ summaryStats.summarysales.orders }}</dd>
			    <dt>{% trans %}TXT_CLIENTS{% endtrans %} ({% trans %}TXT_TODAY{% endtrans %} / {% trans %}TXT_CURRENT_MONTH{% endtrans %})</dt><dd>{{ summaryStats.todayclients.totalclients }} / {{ summaryStats.summaryclients.totalclients }} </dd>
			</dl>
		</div>
		<div class="column wide">
			<div class="tabs">
				<ul>
					<li><a href="#desktop-simple-stats-sales">{% trans %}TXT_SALES{% endtrans %}</a></li>
					<li><a href="#desktop-simple-stats-orders">{% trans %}TXT_ORDERS{% endtrans %}</a></li>
					<li><a href="#desktop-simple-stats-customers">{% trans %}TXT_CLIENTS{% endtrans %}</a></li>
				</ul>
			</div>
			<div class="box">
				<div class="field-text" >
					<label for="desktop-simple-stats-orders-range">{% trans %}TXT_PERIOD_LIST{% endtrans %}:</label>
					<span class="field" style="width: 150px;">
						<input type="text" id="period" class="period" style="width:142px" value="{{ from }} - {{ to }}" />
					</span>
				</div>
				<div id="desktop-simple-stats-sales">
					<div class="chart" id="desktop-simple-stats-sales-chart"></div>
				</div>
				<div id="desktop-simple-stats-orders">
					<div class="chart" id="desktop-simple-stats-orders-chart"></div>
				</div>
				<div id="desktop-simple-stats-customers">
					<div class="chart" id="desktop-simple-stats-customers-chart"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="layout-four-columns">
		<div class="column">
			<div class="box simple">
				<h3><img src="{{ DESIGNPATH }}_images_panel/icons/blocks/recent-orders.png" alt=""/>{% trans %}TXT_LAST_OPINIONS{% endtrans %}</h3>
				<table cellspacing="0" class="full-size">
					<thead>
						<tr>
							<th>{% trans %}TXT_NICK{% endtrans %}</th>
							<th>{% trans %}TXT_CONTENT{% endtrans %}</th>
						</tr>
					</thead>
					<tbody>
						{% for opinion in opinions %}
						<tr class="{{ cycle(['o', 'e'], order) }}">
							<th scope="row"><a title="{{ opinion.nick }} - {{ opinion.review }}" href="{{ URL }}productrange/edit/{{ opinion.idproductreview }}">{{ opinion.nick }}</a></th>
							<td>
								<div style="max-width:170px;">{{ opinion.review }}</div>
							</td>
						</tr>
						{% endfor %}
					</tbody> 
				</table>
				<p class="more"><a class="btnMore" href="{{ URL }}productrange"><span>{% trans %}TXT_SHOW_ALL{% endtrans %}</span></a></p>
			</div>
		</div>

		<div class="column">
			<div class="box simple">
				<h3><img src="{{ DESIGNPATH }}_images_panel/icons/blocks/recent-orders.png" alt=""/>{% trans %}TXT_LAST_ORDERS{% endtrans %}</h3>
				<table cellspacing="0" class="full-size">
					<thead>
						<tr>
							<th>Zamawiający</th>
							<th>{% trans %}TXT_SUM{% endtrans %}</th>
						</tr>
					</thead>
					<tbody>
						{% for order in lastorder %}
						<tr class="{{ cycle(['o', 'e'], order) }}">
							<th scope="row"><a title="{{ order.surname }}" href="{{ URL }}order/edit/{{ order.id }}">{{ order.surname }}</a></th>
							<td>{{ order.price }}</td>
						</tr>
						{% endfor %}
					</tbody>
				</table>
				<p class="more"><a class="btnMore" href="{{ URL }}order"><span>{% trans %}TXT_SHOW_ALL{% endtrans %}</span></a></p>
			</div>
		</div>
		
		<!-- begin: New customers -->
		<div class="column">
			<div class="box simple">
				<h3><img src="{{ DESIGNPATH }}_images_panel/icons/blocks/new-customers.png" alt=""/>{% trans %}TXT_NEW_CUSTOMERS{% endtrans %}</h3>
				<table cellspacing="0" class="full-size">
					<thead>
						<tr>
							<th>{% trans %}TXT_FIRSTNAME{% endtrans %}</th>
							<th abbr="Sztuk">{% trans %}TXT_SURNAME{% endtrans %}</th>
						</tr>
					</thead>
					<tbody>
						{% for client in newclient %}
							<tr class="{{ cycle(['o', 'e'], client) }}">
								<th scope="row"><a title="{{ client.firstname }}" href="{{ URL }}client/edit/{{ client.id }}">{{ client.firstname }}</a></th>
								<td>{{ client.surname }}</td>
							</tr>
						{% endfor %}
					</tbody>
				</table>
				<p class="more"><a class="btnMore" href="{{ URL }}client"><span>{% trans %}TXT_SHOW_ALL{% endtrans %}</span></a></p>
			</div>
		</div>
		<!-- end: New customers -->
		
		<!-- begin: Bestsellers -->
		<div class="column">
			<div class="box simple">
				<h3><img src="{{ DESIGNPATH }}_images_panel/icons/blocks/bestsellers.png" alt=""/>{% trans %}TXT_BESTSELLERS{% endtrans %}</h3>
				<table cellspacing="0" class="full-size">
					<thead>
						<tr>
							<th>{% trans %}TXT_PRODUCT{% endtrans %}</th>
							<th abbr="Sztuk">{% trans %}TXT_QTY{% endtrans %}</th>
						</tr>
					</thead>
					<tbody>
						{% for top in topten %}
						<tr class="{{ cycle(['o', 'e'], top) }}">
							<th scope="row"><div style="max-width:107px;">{% if top.productid > 0 %}<a href="{{ URL }}product/edit/{{ top.productid }}">{{ top.label }}</a>{% else %}{{ top.label }}{% endif %}</div></th>
							<td>{{ top.value }}</td>
						</tr>
						{% endfor %}
					</tbody>
				</table>
				<p class="more"><a class="btnMore" href="{{ URL }}statsproducts"><span>{% trans %}TXT_SHOW_RAPORTS{% endtrans %}</span></a></p>
			</div>
		</div>
		<!-- end: Bestsellers -->
		
		<!-- begin: Most popular -->
		<!-- <div class="column">
			<div class="box simple">
				<h3><img src="{{ DESIGNPATH }}_images_panel/icons/blocks/most-popular.png" alt=""/>{% trans %}TXT_MOST_SEARCH{% endtrans %}</h3>
				<table cellspacing="0" class="full-size">
					<thead>
						<tr>
							<th>{% trans %}TXT_PRODUCT{% endtrans %}</th>
							<th>{% trans %}TXT_QTY{% endtrans %}</th>
						</tr>
					</thead>
					<tbody>
						{% for search in mostsearch %}
						<tr class="{{ cycle(['o', 'e'], search) }}">
							<th scope="row" title="{{ search.productname }}">{{ search.productname }}</th>
							<td>{{ search.qty }}</td>
						</tr>
						{% endfor %}
					</tbody>
				</table>
				<p class="more"><a class="btnMore" href="{{ URL }}mostsearch"><span>{% trans %}TXT_SHOW_RAPORTS{% endtrans %}</span></a></p>
			</div>
		</div> -->
		<!-- end: Most popular -->
		
		<!-- begin: Most popular -->		
		<div class="column">
			<div class="box simple">
				<h3><img src="{{ DESIGNPATH }}_images_panel/icons/blocks/users-online.png" alt=""/>{% trans %}TXT_CLIENT_ONLINE{% endtrans %}</h3>
				<table cellspacing="0" class="full-size">
					<thead>
						<tr>
							<th>{% trans %}TXT_CLIENT{% endtrans %}</th>
							<th>{% trans %}TXT_CART{% endtrans %}</th>
						</tr>
					</thead>
					<tbody>
						{% for online in clientOnline %}
						<tr class="{{ cycle(['o', 'e'], online) }}">
							<th scope="row">{{ online.client }}</th>
							<td>{{ online.cart }}</td>
						</tr>
						{% endfor %}
					</tbody>
				</table>
				<p class="more"><a class="btnMore" href="{{ URL }}spy"><span>{% trans %}TXT_SHOW_RAPORTS{% endtrans %}</span></a></p>
			</div>
		</div>
		<!-- end: Most popular -->
		
	</div>
	<!-- end: Four columns -->
	
</div>
{% endblock %}				
{% block sticky %}
{% include sticky %}
{% endblock %}