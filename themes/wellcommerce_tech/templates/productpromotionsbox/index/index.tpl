{% extends "layoutbox.tpl" %}
{% block content %}
<div class="head-block">
	<span class="font">{{ box.heading }}</span>
	{% if CURRENT_CONTROLLER == 'mainside' %}
    <a href="{{ path('frontend.productpromotion') }}" class="pull-right">{% trans %}TXT_SHOW_ALL{% endtrans %} <i class="icon-arrow-right-blue"></i></a>
    {% endif %}
</div>

{% if dataset.rows|length > 0 %}
	{% if pagination == 1 %}
		{% include 'pagination.tpl' %}
	{% endif %}
	{% include 'products.tpl' %}
	{% if pagination == 1 %}
		{% include 'pagination.tpl' %}
	{% endif %}
{% endif %}
{% endblock %}