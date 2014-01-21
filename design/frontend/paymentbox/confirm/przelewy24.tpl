{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<p>Dziękujemy za dokonanie płatności poprzez system Przelewy24.pl</p>
			<p>Twój numer zamówienia: <strong> {{ orderId }} </strong></p>
		</div>
	</div>
</div>
{% endblock %}
