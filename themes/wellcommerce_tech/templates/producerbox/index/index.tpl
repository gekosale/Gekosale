{% extends "layoutbox.tpl" %}
{% block content %}
{% if CURRENT_PARAM != '' and CURRENT_CONTROLLER == 'producerlist' %}
<nav class="category-nav well">
 <ul class="unstyled">
	{% for producer in producers %}
	<li class="main {% if producer.active %}active{% endif %}">
		<a href="{{ producer.link }}" title="{{ producer.name }}">
			{{ producer.name }}
		</a>
		{% if producer.collections is not empty and producer.active %}
	    <ul class="nav nav-pills nav-stacked">
	    	{% for collection in producer.collections %}
			<li {% if collection.active %}class="active"{% endif %}><a href="{{ collection.link }}">{{ collection.name }}</a></li>
			{% endfor %}   
	    </ul>
		{% endif %}
	</li>
    {% endfor %} 
 </ul>  
</nav>
{% else %}
{% include 'cmsmenubox/index/index.tpl' %}
{% endif %}
{% endblock %}