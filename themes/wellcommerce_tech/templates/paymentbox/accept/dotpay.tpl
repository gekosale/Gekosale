{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Trwa przekierowanie na strony systemu płatności Dotpay.pl. Proszę czekać...</h3>
	</div>
	</div>
</div>
<form action="https://ssl.dotpay.pl/" method="post" id="dotpay">
	<input type="hidden" name="id" value="{{ content.idsprzedawcy }}">
	<input type="hidden" name="amount" value="{{ orderData.priceWithDispatchMethod }}">
	<input type="hidden" name="currency" value="{{ currencySymbol }}">
	<input type="hidden" name="description" value="{% trans %}TXT_ORDER{% endtrans %} {{ orderId }}">
	<input type="hidden" name="lang" value="{{ languageCode }}">
	<input type="hidden" name="email" value="{{ orderData.contactData.email }}">
	<input type="hidden" name="firstname" value="{{ orderData.clientaddress.firstname }}">
	<input type="hidden" name="lastname" value="{{ orderData.clientaddress.surname }}">
	<input type="hidden" name="control" value="{{ content.crc }}">
	<input type="hidden" name="URL" value="{{ path('frontend.payment', {"action": "confirm", "param": "dotpay"}) }}">
	<input type="hidden" name="typ" value="3">
	<input type="hidden" name="URLC" value="{{ path('frontend.payment', {"action": "report", "param": "dotpay"}) }}">
	<input type="hidden" name="txtguzik" value="Powrót do {{ SHOP_NAME }} ">
	<input type="hidden" name="street" value="{{ orderData.clientaddress.street }}">
	<input type="hidden" name="street_n1" value="{{ orderData.clientaddress.streetno }}">
	{% if orderData.clientaddress.placeno != '' %}<input type="hidden" name="street_n2" value="{{ orderData.clientaddress.placeno }}">{% endif %}
	<input type="hidden" name="city" value="{{ orderData.clientaddress.placename }}">
	<input type="hidden" name="postcode" value="{{ orderData.clientaddress.postcode }}">
	<input type="hidden" name="phone" value="{{ orderData.contactData.phone }}">
	<input type="hidden" name="country" value="Polska">
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#dotpay').submit();
});
</script>
{% endblock %}