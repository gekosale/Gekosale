{% if pagination == 1 and dataset.totalPages|length > 1 %}
<div class="pagination pagination-centered">
	<ul>
		{% for link in paginationLinks %}
			<li class="{{ link.class }}"><a href="{{ link.link }}">{{ link.label }}</a></li>
		{% endfor %}
	</ul>
</div>
{% endif %}