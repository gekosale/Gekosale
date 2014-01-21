{% extends "layoutbox.tpl" %}
{% block content %}
{% if orderId > 0 %}
	<div>
		<p>Dziękujemy za dokonanie płatności poprzez system Transferuj.pl</p>
		<p>Twój numer zamówienia: <strong> {{ orderId }} </strong></p>
	</div>
{% endif %}
<div class="buttons">
	<a href="{{ path('frontend.home') }}" class="button"><span>{% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</span></a>
</div>
{% endblock %}
