{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/configuration-list.png" alt=""/>{% trans %}TXT_INSTANCE_MANAGER{% endtrans %}</h2>
<div class="block"><p>Trwa przekierowywanie na stronę Przelewy24.pl...</p></div>

<form accept-charset="ISO-8859-2" action="https://secure.przelewy24.pl" method="post" id="przelewy24">
    <input type="hidden" name="p24_session_id" value="{{ paymentData.p24_session_id }}" />
	<input type="hidden" name="p24_id_sprzedawcy" value="{{ paymentData.p24_id_sprzedawcy }}">
	<input type="hidden" name="p24_kwota" value="{{ paymentData.p24_kwota }}">
    <input type="hidden" name="p24_klient" value="{{ paymentData.p24_klient }}" />
    <input type="hidden" name="p24_adres" value="{{ paymentData.p24_adres }}" /> 
    <input type="hidden" name="p24_kod" value="{{ paymentData.p24_kod }}" />
    <input type="hidden" name="p24_miasto" value="{{ paymentData.p24_miasto }}" />
    <input type="hidden" name="p24_kraj" value="{{ paymentData.p24_kraj }}" />
    <input type="hidden" name="p24_email" value="{{ paymentData.p24_email }}">
    <input type="hidden" name="p24_return_url_ok" value="{{ paymentData.p24_return_url_ok }}" />
    <input type="hidden" name="p24_return_url_error" value="{{ paymentData.p24_return_url_error }}" />
    <input type="hidden" name="p24_opis" value="{{ paymentData.p24_opis }}" />
    <input type="hidden" name="p24_language" value="pl" />
    <input type="hidden" name="p24_crc" value="{{ paymentData.p24_crc }}" />
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#przelewy24').submit();
});
</script>
{% endblock %}
