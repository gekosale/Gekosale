{% if orderId > 0 %}
<div>
	<p>Dziękujemy za dokonanie płatności poprzez system Paypal</p>
	<p>Twój numer zamówienia: <strong> {{ orderId }} </strong></p>
</div>	
{% endif %}
<div class="buttons">
	<a href="{{ path('frontend.home') }}"><img src="{{ DESIGNPATH }}/_images_frontend/buttons/wroc-do-zakupow.png" alt="{% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}"/></a>
</div>		