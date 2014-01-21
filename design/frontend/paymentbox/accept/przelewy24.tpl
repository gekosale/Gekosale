{% extends "layoutbox.tpl" %}
{% block content %}
<form action="https://secure.przelewy24.pl" method="post" id="przelewy24">
    <input type="hidden" name="p24_session_id" value="{{ content.sessionid }}" />
	<input type="hidden" name="p24_id_sprzedawcy" value="{{ content.idsprzedawcy }}">
	<input type="hidden" name="p24_kwota" value="{{ content.kwota }}">
    <input type="hidden" name="p24_klient" value="{{ orderData.clientaddress.firstname }} {{orderData.clientaddress.surname }}" />
    <input type="hidden" name="p24_adres" value="{{ orderData.clientaddress.street }} {{orderData.clientaddress.streetno }}{% if orderData.clientaddress.placeno != '' %}/{{orderData.clientaddress.placeno }}{% endif %}" /> <!-- odbiorca zamówienia, ulica, numer domu i lokalu -->
    <input type="hidden" name="p24_kod" value="{{ orderData.clientaddress.postcode }}" />
    <input type="hidden" name="p24_miasto" value="{{ orderData.clientaddress.placename }}" />
    <input type="hidden" name="p24_kraj" value="PL" />
    <input type="hidden" name="p24_email" value="{{ orderData.contactData.email }}">
    <input type="hidden" name="p24_return_url_ok" value="{{ path('frontend.payment', {"action": 'report', "param": 'przelewy24'}) }}" />
    <input type="hidden" name="p24_return_url_error" value="{{ path('frontend.payment', {"action": 'report', "param": 'przelewy24'}) }}" />
    <input type="hidden" name="p24_opis" value="{% trans %}TXT_ORDER{% endtrans %} {{orderId }}" />
    <input type="hidden" name="p24_language" value="pl" />
    <input type="hidden" name="p24_crc" value="{{ content.crc }}" />
</form>

<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Trwa przekierowanie na strony systemu płatności Przelewy24.pl. Proszę czekać...</h3>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#przelewy24').submit();
});
</script>
{% endblock %}	
