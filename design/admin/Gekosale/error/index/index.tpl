{% extends "layout.tpl" %}
{% block content %}
<h2>Wystąpił błąd</h2>
<div class="block">
	<p>{{ errorMsg }}</p>
</div>
{{ form }}
{% endblock %}