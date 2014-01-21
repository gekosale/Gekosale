{% extends "layoutbox.tpl" %}
{% block content %}
{% if orderlist|length > 0 %}
<article class="article">
	<h1>{% trans %}TXT_CLIENT_ORDER_HISTORY{% endtrans %}</h1>
    <table class="table table-striped table-bordered history-order">
    	<thead>
        	<tr class="thead-info">
            	<td colspan="6">Poniżej są wyświetlone wszystkie Twoje zamówienia posortowane według daty dodania.</td>
            </tr>
            <tr>
            	<th>{% trans %}TXT_ORDER{% endtrans %}</th>
                <th>{% trans %}TXT_DATE{% endtrans %}</th>
                <th>{% trans %}TXT_SUM{% endtrans %}</th>
                <th>{% trans %}TXT_PAYMENT{% endtrans %}</th>
                <th>{% trans %}TXT_STATUS{% endtrans %}</th>
                <th>{% trans %}TXT_OPTIONS{% endtrans %}</th>
			</tr>
		</thead>
        <tbody>
        	{% for other in orderlist %}
        	<tr>
            	<td>{{ other.idorder }}</td>
                <td>{{ other.orderdate }}</td>
                <td><strong>{{ other.globalprice }} {{ other.currencysymbol }}</strong></td>
                <td>{{ other.paymentmethodname }}</td>
                <td style="color: #{{ other.colour }};">{{ other.orderstatusname }}</td>
                <td><a href="{{ path('frontend.clientorder', {"param": other.idorder}) }}" title="">{% trans %}TXT_SHOW{% endtrans %}</a></td>
			</tr>
			{% endfor %}
		</tbody>
	</table>                        
</article>
{% else %}
<div class="alert alert-block alert-info">
	Do tej pory nie złożyłeś jeszcze żadnego zamówienia! Nie czekaj, zobacz naszą ofertę, a na pewno znajdziesz coś dla siebie!
</div>
{% endif %}
<div class="head-block">
	<span class="font">Oferta dla Ciebie</span>
</div>
<div id="recommendations">
	{{ recommendations(4) }}
</div>
{% endblock %}