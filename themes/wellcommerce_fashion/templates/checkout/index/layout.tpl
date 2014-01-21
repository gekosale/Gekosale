{% include 'header_checkout.tpl' %} {% block header %}
<header class="header">
	<h1>
		<a href="{{ path('frontend.home') }}" title="{{ SHOP_NAME }}">{{ SHOP_NAME }}</a>
	</h1>
	<div class="font order-step current-step1">
		<div class="step step1 current">
			<strong>{% trans %}TXT_CHECKOUT_STEP1{% endtrans %}</strong> <span class="desc">{% trans %}TXT_CHECKOUT_ADDRESS_DATA{% endtrans %}</span>
		</div>
		<div class="step step2">
			<strong>{% trans %}TXT_CHECKOUT_STEP2{% endtrans %}</strong> <span class="desc">{% trans %}TXT_CHECKOUT_SUMMARY{% endtrans %}</span>
		</div>
		<div class="step step3">
			<strong>{% trans %}TXT_CHECKOUT_STEP3{% endtrans %}</strong> <span class="desc">{% trans %}TXT_CHECKOUT_PAYMENT{% endtrans %}</span>
		</div>
	</div>
</header>
{% endblock %}
<div class="order-wrap">
	<section id="content" class="content layout-boxes">
	{{ pagescheme.content }}
	</section>
</div>
{{ pagescheme.js }} 
{% include 'footer.tpl' %}
