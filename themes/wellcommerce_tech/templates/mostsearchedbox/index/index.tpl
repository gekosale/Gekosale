{% extends "layoutbox.tpl" %}
{% block content %}
{% if mostsearched[0] is defined %}	
	<div id="cloud">
		{% for tag in mostsearched %}
			<a class="tag{{ tag.percentage }}" href="{{ path('frontend.productsearch') }}/{{ tag.phrase }}">{{ tag.name }}</a>
		{% endfor %}
	</div>
	{% else %}
	<p>{% trans %}ERR_EMPTY_TAGS{% endtrans %}</p>
{% endif %}
{% endblock %}
