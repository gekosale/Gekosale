{% extends "layoutbox.tpl" %} 
{% block content %}
<article class="article">
	<h1>{{ box.heading }}</h1>
	<div class="alert alert-error">{% trans %}TXT_INVALID_LINK{% endtrans %}</div>
</article>
{% endblock %}
