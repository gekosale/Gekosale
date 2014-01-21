{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/orders-edit.png" alt=""/>{% trans %}TXT_SPY{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}spy" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SPY_LIST{% endtrans %}" alt="{% trans %}TXT_SPY_LIST{% endtrans %}"/></span></a></li>
</ul>

<script type="text/javascript">
GCore.OnLoad(function() {
	$('.view-order').GTabs();
});
</script>

<div class="view-order GForm">
	
	<fieldset>
		<legend><span>{% trans %}TXT_CART{% endtrans %}</span></legend>
			{% if cart|length == 0 %}
				<p>{% trans %}TXT_CART_IS_EMPTY{% endtrans %}</p>
			{% else %}
			<ul class="changes-detailed">
			{% for product in cart %}
				{% if product.attributes is defined %}
				{% for attribute in product.attributes %}
				<li>
					<p>{% trans %}TXT_PRODUCT{% endtrans %}:  <strong>{{ attribute.name }}</strong></p>
					<p>{% trans %}TXT_QUANTITY{% endtrans %}: <strong>{{ attribute.qty }}</strong> {% trans %}TXT_QTY{% endtrans %}</p>
					<p>{% trans %}TXT_PRICE{% endtrans %}: <strong>{{ attribute.qtyprice|priceFormat }}</strong></p>
				</li>
				{% endfor %}
				{% else %}
				<li>
					<p>{% trans %}TXT_PRODUCT{% endtrans %}:  <strong>{{ product.name }}</strong></p>
					<p>{% trans %}TXT_QUANTITY{% endtrans %}: <strong>{{ product.qty }}</strong> {% trans %}TXT_QTY{% endtrans %}</p>
					<p>{% trans %}TXT_PRICE{% endtrans %}: <strong>{{ product.qtyprice|priceFormat }}</strong></p>
				</li>
				{% endif %}
			{% endfor %}
			</ul>
			{% endif %}
			
	</fieldset>
	
	<fieldset>
		<legend><span>{% trans %}TXT_CLIENT{% endtrans %}</span></legend>
		{% if clientData.firstname is not defined %}
			<p>{% trans %}TXT_GUEST{% endtrans %}</p>
		{% else %}
			<p>{% trans %}TXT_FIRSTNAME{% endtrans %}:  <strong>{{ clientData.firstname }}</strong></p>
			<p>{% trans %}TXT_SURNAME{% endtrans %}: <strong>{{ clientData.surname }}</strong></p>
		{% endif %}
	</fieldset>
	
	<fieldset>
		<legend><span>Historia zamówień klienta</span></legend>
			{% if clientData.firstname is not defined %}
				<p>{% trans %}TXT_GUEST{% endtrans %}</p>
			{% else %}
			<ul class="changes-detailed">
				{% if clientOrderHistory[0].adddate is defined %}
				{% for clientorderhistory in clientOrderHistory %}
					<li>
						<h4><span>{{ clientorderhistory.adddate }}</span></h4>
						<p>Nr. zamówienia:  <strong><a href="{{ URL }}order/edit/{{ clientorderhistory.idorder }}">#{{ clientorderhistory.idorder }}</a></strong></p>
						<p class="author">Wartość zamówienia : <strong>{{ clientorderhistory.globalprice }}</strong>{% trans %}TXT_CURRENCY{% endtrans %}</p>
					</li>
				{% endfor %}
				{% else %}
				Brak zamówień
				{% endif %}
			</ul>
			{% endif %}
	</fieldset>
{% endblock %}