{% for item in dataset.rows %}
<div class="product-list row-fluid">
	<div class="span3 photo">
		<div class="labels">
			<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}">
				{% if item.discountprice > 0 %}
	        	<span class="label label-promotion">{% trans %}TXT_PROMOTION{% endtrans %}</span>
	        	{% endif %}
	        	{% if item.new == 1 %}
	        	<span class="label label-new">{% trans %}New product{% endtrans %}</span>
	        	{% endif %}
	        	{% for status in item.statuses %}
	            <span class="label label-{{ status.symbol }}">{{ status.name }}</span>
	            {% endfor %}
            </a>
        </div>
    	<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}"><img src="{{ item.photo }}" alt="{{ item.name }}"></a>
	</div>
    <div class="span6 info">
		<a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="{{ item.name }}"><h3>{{ item.name }}</h3></a>
        {{ item.shortdescription }}
	</div>
	<div class="span3">
	{% if ( item.discountprice != NULL and item.discountprice != item.price ) %}
		<p class="price promo">{% if showtax == 0 %}{{ item.discountpricenetto|priceFormat }}{% else %}{{ item.discountprice|priceFormat }}{% endif %} <small>{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</small></small></p>
    {% else %}
    	<p class="price">{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</p>
    {% endif %}
    	{% if item.onstock == 1 %}
        <p class="action"><a href="#" class="btn btn-primary" onclick="xajax_doQuickAddCart({{ item.id }});return false;"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_ADD_TO_CART{% endtrans %}</a></p>
        {% else %}
        <p class="action"><a class="btn btn-danger" href="{{ path('frontend.contact', {"param": item.id }) }}"> {% trans %}TXT_REQUEST_QUOTE{% endtrans %}</a></p>
        {% endif %}
        <a href="{{ path('frontend.productcart', {"param": item.seo}) }}" class="more">Więcej o produkcie <i class="icon-arrow-right-blue"></i></a>
	</div>
</div>
{% endfor %}