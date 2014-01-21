{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Trwa przekierowanie na strony systemu płatności Transferuj.pl. Proszę czekać...</h3>
		</div>
	</div>
</div>
<form action="https://secure.transferuj.pl" method="post" id="transferuj">
	<input type="hidden" name="id" value="{{ content.idsprzedawcy }}">
	<input type="hidden" name="kwota" value="{{ content.amount }}">
	<input type="hidden" name="opis" value="{% trans %}TXT_ORDER{% endtrans %} {{ orderId }}">
	<input type="hidden" name="crc" value="{{ content.crc }}">
	<input type="hidden" name="md5sum" value="{{ content.md5sum }}">
	<input type="hidden" name="wyn_url" value="{{ path('frontend.payment', {"action": "report", "param": "transferuj"}) }}">
	<input type="hidden" name="opis_sprzed" value="{{ SHOP_NAME }}">
	<input type="hidden" name="pow_url" value="{{ path('frontend.payment', {"action": "confirm", "param": "transferuj"}) }}">
	<input type="hidden" name="pow_url_blad" value="{{ path('frontend.payment', {"action": "cancel", "param": "transferuj"}) }}">
	<input type="hidden" name="pow_tekst" value="Powrót do {{ SHOP_NAME }} ">
	<input type="hidden" name="email" value="{{ orderData.contactData.email }}">
	<input type="hidden" name="nazwisko" value="{{ orderData.clientaddress.surname }}">
	<input type="hidden" name="imie" value="{{ orderData.clientaddress.firstname }}">
	<input type="hidden" name="adres" value="{{ orderData.clientaddress.street }} {{ orderData.clientaddress.streetno }} {% if orderData.clientaddress.placeno != '' %}/{{ orderData.clientaddress.placeno }}{% endif %}">
	<input type="hidden" name="miasto" value="{{ orderData.clientaddress.placename }}">
	<input type="hidden" name="kod" value="{{ orderData.clientaddress.postcode }}">
	<input type="hidden" name="kraj" value="Polska">
	<input type="hidden" name="telefon" value="{{ orderData.contactData.phone }}">
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#transferuj').submit();
});
</script>
{% endblock %}