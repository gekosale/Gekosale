<div class="carousel-inner">
	{% for item in items %}
	<div class="item {% if loop.first %}active{% endif %}">
		<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}">
			<img src="{{ item.photo }}" alt="{{ item.name }}"/>
		</a>
		<div class="carousel-caption">
			<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}">
				<h2>{{ item.name }}</h2>
			</a>
			{% if ( item.discountprice != NULL and item.discountprice != item.price ) %}
			<p class="price promo">{% if showtax == 0 %}{{ item.discountpricenetto|priceFormat }}{% else %}{{ item.discountprice|priceFormat }}{% endif %} <small>{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</small></p>
			{% else %}
			<p class="price">{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</p>
			{% endif %}
			<a class="btn btn-primary" onclick="xajax_doQuickAddCart({{ item.id }});return false;" href="{{ path('frontend.productcart', {"param": item.seo}) }}"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_ADD_TO_CART{% endtrans %}</a>
		</div>
	</div>
	{% endfor %}
</div>
<a class="left carousel-control" href="#myCarousel"	data-slide="prev"></a>
<a class="right carousel-control" href="#myCarousel" data-slide="next"></a>