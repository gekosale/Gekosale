{% extends "layoutbox.tpl" %}
{% block content %}
<h2 class="category-title">{{ box.heading }}</h2>
{% if currentCategory.description != '' or currentCategory.shortdescription !='' or currentCategory.photo != '' %}
<div class="category-description">
	{% if currentCategory.photo != '' %}
	<img src="{{ currentCategory.photo }}" alt="{{ currentCategory.name }}" />
	{% endif %}
	<div class="caption">
	{% if currentCategory.description !='' %}
		{{ currentCategory.description }}
	{% else %}
		{{ currentCategory.shortdescription }}
	{% endif %}
	</div>
</div>
{% endif %}

{% include 'pagination.tpl' %} 
{% include 'products.tpl' %}
{% include 'pagination.tpl' %}
{% endblock %}