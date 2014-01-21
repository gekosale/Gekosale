{% extends "layoutbox.tpl" %}
{% block content %}
<h2 class="category-title">{{ box.heading }}</h2>
{% if currentCategory.description != '' or currentCategory.shortdescription !='' or currentCategory.photo != '' %}
<div class="category-description" {% if currentCategory.photo != '' %}style="background: transparent url('{{ currentCategory.photo }}') no-repeat top right;"{% endif %}>
	<div class="caption">
	{% if currentCategory.description !='' %}
		{{ currentCategory.description }}
	{% else %}
		{{ currentCategory.shortdescription }}
	{% endif %}
	</div>
</div>
{% endif %}
<div class="alert alert-block alert-error">
<strong>{% trans %}TXT_NO_PRODUCTS{% endtrans %}</strong>
</div>
{% include 'products.tpl' %}
{% endblock %}