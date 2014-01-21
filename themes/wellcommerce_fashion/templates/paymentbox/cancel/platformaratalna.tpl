{% if orderId > 0 %}
<div>
	<p>{% trans %}TXT_PLATNOSCI_CANCELLED{% endtrans %}</p>
	<p>Twój numer zamówienia: <strong> {{ orderId }} </strong></p>
</div>	
{% endif %}
<div class="buttons">
	<a href="{{ path('frontend.home') }}" class="button"><span>{% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</span></a>
</div>	