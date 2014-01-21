{% if dataset.total > 0 %}
<ul>
	{% for links in dataset.totalPages %}
			<li class="page {% if dataset.totalPages[links] == dataset.activePage %}active{% endif %}" ><a href="{{ path('frontend.home') }}{seo controller=$controller seo=$currentCategory.seo page=$dataset.totalPages[links] price=$priceRange producers=$currentProducers attributes=$currentAttributes }">{{ dataset.totalPages[links] }}</a></li>
	{% endfor %}
</ul>
{% endif %}
