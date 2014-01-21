{% extends "layoutbox.tpl" %}
{% block content %}
<div class="head-block">
	<span class="font">{{ box.heading }}</span>
</div>
{% include 'products.tpl' %}
{% endblock %}