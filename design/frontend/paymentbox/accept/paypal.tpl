{% extends "layoutbox.tpl" %}
{% block content %}
<div><p>Trwa przekierowanie na strony systemu Paypal. Proszę czekać...</p></div>
	<form id="paypal" action="{{ content.gateway }}" method="POST">
		<input type="hidden" name="rm" value="{{ content.rm }}">
		<input type="hidden" name="cmd" value="{{ content.cmd }}">
		<input type="hidden" name="business" value="{{ content.business }}">
		<input type="hidden" name="currency_code" value="{{ content.currency_code }}">
		<input type="hidden" name="return" value="{{ content.return }}">
		<input type="hidden" name="cancel_return" value="{{ content.cancel_return }}">
		<input type="hidden" name="notify_url" value="{{ content.notify_url }}">
		<input type="hidden" name="item_name" value="{{ content.item_name }}">
		<input type="hidden" name="amount" value="{{ content.amount }}">
		<input type="hidden" name="item_number" value="{{ content.item_number }}">
		<input type="hidden" name="custom" value="{{ content.session_id }}">
	</form>
<script type="text/javascript">

$(document).ready(function(){
	$('#paypal').submit();
});

</script>
{% endblock %}