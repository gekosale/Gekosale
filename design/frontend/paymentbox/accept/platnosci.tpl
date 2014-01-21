{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Trwa przekierowanie na strony systemu płatności Platnosci.pl. Proszę czekać...</h3>
		</div>
	</div>
</div>
<form action="https://www.platnosci.pl/paygw/UTF/NewPayment" method="post" id="platnosci">
	<input type="hidden" name="language" value="{{ content.language }}">
	<input type="hidden" name="session_id" value="{{ content.session_id }}">
	<input type="hidden" name="order_id" value="{{ content.order_id }}">
	<input type="hidden" name="js" value="1">
	<input type="hidden" name="pos_id" value="{{ content.pos_id }}">
	<input type="hidden" name="pos_auth_key" value="{{ content.pos_auth_key }}">
	<input type="hidden" name="amount" value="{{ content.amount }}">
	<input type="hidden" name="desc" value="{{ content.desc }}">
	<input type="hidden" name="desc2" value="{{ content.desc2 }}">
	<input type="hidden" name="first_name" value="{{ content.first_name }}">
	<input type="hidden" name="last_name" value="{{ content.last_name }}">
	<input type="hidden" name="street" value="{{ content.street }}">
	<input type="hidden" name="street_hn" value="{{ content.street_hn }}">
	<input type="hidden" name="city" value="{{ content.city }}">
	<input type="hidden" name="post_code" value="{{ content.post_code }}">
	<input type="hidden" name="country" value="{{ content.country }}">
	<input type="hidden" name="phone" value="{{ content.phone }}">
	<input type="hidden" name="email" value="{{ content.email }}">
	<input type="hidden" name="client_ip" value="{{ content.client_ip }}">
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#platnosci').submit();
});
</script>
{% endblock %}