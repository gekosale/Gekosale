{% if items|length > 0 %}
<div style="z-index: 900000; width: 250px;right: 289px;background: #fff;" > 
{% for item in items %}
<div class="product-list row-fluid">
	<div class="span3 photo">
    	<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}"><img src="{{ item.photo }}" alt="{{ item.name }}"></a>
	</div>
    <div class="span9 info">
		<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}"><h4 style="padding: 5px;">{{ item.name }}</h4></a>
	</div>
</div>
{% endfor %}
</div>
</div>
{% endif %}