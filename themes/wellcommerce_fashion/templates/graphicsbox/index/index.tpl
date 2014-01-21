{% extends "layoutbox.tpl" %}
{% block content %}
{% if url == '' %}
	<span style="display:block;width:100%;{{ style }}"></span>
{% else %}
	<a href="{{ url }}" style="display:block;width:100%;{{ style }}"></a>
{% endif %}
{% endblock %}