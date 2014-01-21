{% if view == 0 %}
	{% include 'products_grid.tpl' %}
{% else %}
	{% include 'products_list.tpl' %}
{% endif %}