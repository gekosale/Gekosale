<div class="layout-box">
	<div class="layout-box-header">
		<h3 style="cursor: pointer;">{% trans %}TXT_CONFIRMATION_ORDER{% endtrans %}</h3>
	</div>
	<div class="layout-box-content">
			{% if upateOrder == 1 %}
				{% if upateOrder == 1 %}
					{% trans %}TXT_CONFIRMED_ORDER{% endtrans %}
				{% else %}
						{% trans %}TXT_ERROR_CONFIRMATION_ORDER{% endtrans %}<br/>
						{% trans %}TXT_ERROR_CONFIRMATION_ORDER_INFO{% endtrans %}
				{% endif %}
			{% else %}
				{% trans %}TXT_INVALID_LINK{% endtrans %}
			{% endif %}
	
		<div class="buttons">
			<a href="{{ path('frontend.home') }}" class="button"><span>{% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</span></a>
		</div>	
	</div>
</div>
