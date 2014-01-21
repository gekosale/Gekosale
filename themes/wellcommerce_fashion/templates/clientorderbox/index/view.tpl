{% extends "layoutbox.tpl" %}
{% block content %}
{% autoescape true %}
<article class="article">
	<h1>{% trans %}TXT_ORDER_DETAILS{% endtrans %} #{{ order.idorder }}</h1>
    <div class="well well-clean well-small order-info">
    	<div class="row-fluid">
        	<div class="span6">
            	<div class="item">
                	<span class="name">{% trans %}TXT_STATUS{% endtrans %}:</span>
                    <strong>{{ order.orderstatusname }}</strong>
				</div>
            	<div class="item">
                	<span class="name">{% trans %}TXT_DATE{% endtrans %}:</span>
                    <strong>{{ order.orderdate }}</strong>
				</div>
            	<div class="item">
                	<span class="name">{% trans %}TXT_PAYMENT{% endtrans %}:</span>
                    <strong>{{ order.paymentmethodname }}</strong>
				</div>
            	<div class="item">
                	<span class="name">{% trans %}TXT_EDIT_ORDER_BILLING_DATA{% endtrans %}:</span>
                    <strong>
                    {{ order.billingaddress.firstname }} {{ order.billingaddress.surname }}<br />
					{{ order.billingaddress.street }} {{ order.billingaddress.streetno }} / {{ order.billingaddress.placeno }}<br />
					{{ order.billingaddress.postcode }} {{ order.billingaddress.placename }}<br />
					{{ order.billingaddress.phone }}<br />
					{% if order.billingaddress.phone2 != '' %}{{ order.billingaddress.phone2 }}<br />{% endif %}
					{{ order.billingaddress.email }}
                    </strong>
				</div>
			</div>
            <div class="span6">
            	<div class="item">
                	<span class="name">{% trans %}TXT_DISPATCH{% endtrans %}:</span>
                    <strong>{{ order.dispatchmethodname }}</strong>
				</div>
                <div class="item">
                	<span class="name">{% trans %}TXT_EDIT_ORDER_SHIPPING_DATA{% endtrans %}:</span>
                    <strong>
                    {{ order.shippingaddress.firstname }} {{ order.shippingaddress.surname }}<br />
					{{ order.shippingaddress.street }} {{ order.shippingaddress.streetno }} / {{ order.shippingaddress.placeno }}<br />
					{{ order.shippingaddress.postcode }} {{ order.shippingaddress.placename }}<br />
					{{ order.shippingaddress.phone }}<br />
					{% if order.billingaddress.phone2 != '' %}{{ order.billingaddress.phone2 }}<br />{% endif %}
					{{ order.shippingaddress.email }}
                    </strong>
				</div>
			</div>
		</div>
	</div>
	<table class="table table-striped order">
		<caption>{% trans %}TXT_VIEW_ORDER_PRODUCTS{% endtrans %}</caption>
        <thead>
        	<tr>
            	<th>{% trans %}TXT_PRODUCT_NAME{% endtrans %}</th>
                <th>{% trans %}TXT_PRODUCT_PRICE{% endtrans %}</th>
                <th>{% trans %}TXT_QUANTITY{% endtrans %}</th>
                <th style="width: 90px">{% trans %}TXT_VALUE{% endtrans %}</th>
			</tr>
		</thead>
		<tbody>
			{% for order in orderproductlist %}
			<tr>
				<td><strong>{{ order.productname|raw }}</strong>
				{% for attribute in order.attributes %}						
					<br />{{ attribute.attributegroup }}: {{ attribute.attributename }}
				{% endfor %}
				</td>
				<td>{{ order.price|priceFormat }}</td>
                <td><strong>{{ order.qty|number_format }}</strong></td>
				<td><strong>{{ order.qtyprice|priceFormat }}</strong></td>
			</tr>
			{% endfor %}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" class="alignright"><strong>{{ order.dispatchmethodname }}</strong></td>
				<td><strong>{{ order.dispatchmethodprice|priceFormat }}</strong></td>
			</tr>
			{% if order.coupondiscount > 0 %}
			<tr>
				<td colspan="3" class="alignright"><strong>Kupon rabatowy: {{ order.couponcode }}</strong></td>
				<td><strong class="green">{{ order.coupondiscount|priceFormat }}</strong></td>
			</tr>
			{% endif %}
		</tfoot>
	</table>
	<div class="order-total">
    	<span class="total">{% trans %}TXT_ALL_ORDERS_PRICE{% endtrans %}</span>
        <span class="price">{{ order.globalprice }} {{ order.currencysymbol }}</span>
	</div>
	<table class="table table-striped table-bordered history-order">
    	<thead>
        	<tr class="thead-info">
            	<td colspan="3">{% trans %}TXT_ORDER_INVOICE_LIST{% endtrans %}</td>
            </tr>
            <tr>
            	<th>{% trans %}TXT_INVOICE_NUMBER{% endtrans %}</th>
                <th>{% trans %}TXT_INVOICE_DATE{% endtrans %}</th>
                <th>{% trans %}TXT_MATURITY{% endtrans %}</th>
                <th>{% trans %}TXT_TOTAL_PAYED{% endtrans %}</th>
                <th>{% trans %}TXT_OPTIONS{% endtrans %}</th>
			</tr>
		</thead>
        <tbody>
        	{% for invoice in order.invoices %}
        	<tr>
            	<td>{{ invoice.symbol }}</td>
                <td>{{ invoice.invoicedate }}</td>
                <td>{{ invoice.paymentduedate }}</td>
                <td>{{ invoice.totalpayed }}</td>
                <td><a href="{{ path('frontend.invoice', {"param": invoice.idinvoice}) }}" title="">{% trans %}TXT_DOWNLOAD_INVOICE{% endtrans %} <i class="icon icon-chevron-down"></i></a></td>
			</tr>
			{% else %}
			<tr>
            	<td colspan="5" style="text-align: center;">{% trans %}TXT_EMPTY_INVOICE_LIST{% endtrans %}</td>
			</tr>
			{% endfor %}
		</tbody>
	</table>      
	<div class="pull-right">
    	<a href="{{ path('frontend.clientorder') }}" title=""><i class="icon icon-arrow-left-blue"></i> Wróć do historii zamówień</a>
	</div>
</article>
{% endautoescape %}
<div class="head-block" style="clear: both; margin-top: 50px;">
	<span class="font">Oferta dla Ciebie</span>
</div>
<div id="recommendations">
	{{ recommendations(4) }}
</div>
{% endblock %}