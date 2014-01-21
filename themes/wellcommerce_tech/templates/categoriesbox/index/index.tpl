{% extends "layoutbox.tpl" %}
{% block content %}

<nav class="category-nav well">
 <ul class="unstyled">
	{% for category in categories %}
	
	{% if hideempty == 0 or (hideempty == 1 and category.totalproducts > 0) %}
	<li class="main {% if category.id in path %}active{% endif %}">
	  <a href="{{ category.link }}" title="{{ category.label }}">
		{{ category.label }} {% if showcount == 1 %}({{ category.totalproducts }}){% endif %}
	  </a>
	{% if category.children|length > 0  and category.id in path %}
    <ul class="nav nav-pills nav-stacked">
    	{% for subcategory in category.children %}
    	{% if hideempty == 0 or (hideempty == 1 and subcategory.totalproducts > 0) %}
		<li {% if subcategory.id in path %}class="active"{% endif %}><a href="{{ subcategory.link }}">{{ subcategory.label }} {% if showcount == 1 %}({{ subcategory.totalproducts }}){% endif %}</a></li>
		{% endif %}
		{% endfor %}   
    </ul>
    {% endif %}
    {% endif %}
	</li>
    {% endfor %} 
 </ul>  
</nav>

{% endblock %}