{% extends "layoutbox.tpl" %}
{% block content %}
<nav class="category-nav well">
 <ul class="unstyled">
	{% for cat in contentcategory %}
	<li class="main {% if cat.id in activePath %}active{% endif %}">
		<a href="{{ cat.link }}" title="{{ cat.name }}">
			{{ cat.name }}
		</a>
		{% if cat.children is not empty and cat.id in activePath %}
	    <ul class="nav nav-pills nav-stacked">
	    	{% for subcat in cat.children %}
			<li {% if subcat.id in activePath %}class="active"{% endif %}><a href="{{ subcat.link }}">{{ subcat.name }}</a></li>
			{% endfor %}   
	    </ul>
	    {% endif %}
	</li>
    {% endfor %}   
</nav>
{% endblock %}