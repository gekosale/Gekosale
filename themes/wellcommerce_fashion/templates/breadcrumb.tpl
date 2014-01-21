<ul class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
{% for crumb in breadcrumb %}
   	{% if loop.last %}
	<li class="active" typeof="v:Breadcrumb">{{ crumb.title }}</li>
    {% else %}
    <li typeof="v:Breadcrumb"><a href="{{ path('frontend.home') }}{{ crumb.link }}" rel="v:url" property="v:title">{{ crumb.title }}</a> <span class="divider">/</span></li>
    {% endif %}
{% endfor %}
</ul>