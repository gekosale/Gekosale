{% extends "layoutbox.tpl" %} 
{% block content %}
{% if productCart|length > 0 %}
{% if message is defined %}
<div class="alert alert-error">
	{{ message }}
</div>
{% endif %}
<article class="article marginbt20">
	<div class="row-fluid row-form">
		<div class="basket-large">
			{% if minimumordervalue > 0 %}
			
			{% else %}
			<div class="pull-right">
				<a href="{{ path('frontend.home') }}" title=""><i class="icon icon-arrow-left-blue"></i> {% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</a>
				{% if payments|length and deliverymethods|length %}
				<a href="{{ path('frontend.checkout') }}" class="btn btn-large btn-primary marginlt20">{% trans %}TXT_PLACE_ORDER{% endtrans %}</a>
				{% endif %}
			</div>
			{% endif %}
			<h1>{% trans %}TXT_CART{% endtrans %}</h1>
			{% if payments|length and deliverymethods|length %}
			{% include 'cartbox/index/rules.tpl' %}
			{% endif %}
			<form class="form-horizontal" action="{{ path('frontend.checkout') }}" id="cart-contents">
				{% include 'cartbox/index/table.tpl' %}
			</form>
		</div>
	</div>
</article>
{% else %}
	{% include 'cartbox/index/empty.tpl' %}
{% endif %}	
{% endblock %}
