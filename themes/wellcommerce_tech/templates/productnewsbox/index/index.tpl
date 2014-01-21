{% extends "layoutbox.tpl" %}
{% block content %}
<div class="head-block">
	<span class="font">{% trans %}TXT_NEW_PRODUCTS{% endtrans %}</span>
	{% if CURRENT_CONTROLLER != 'productnews' %}
    <a href="{{ path('frontend.productnews') }}" class="pull-right">{% trans %}TXT_SHOW_ALL{% endtrans %} <i class="icon-arrow-right-blue"></i></a>
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