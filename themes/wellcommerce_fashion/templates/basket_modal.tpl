<button class="close" data-dismiss="modal">&times;</button>
<div class="alert alert-block alert-success">
	<p>"{{ product.productname }}" {% trans %}TXT_CART_PRODUCT_ADDED{% endtrans %}</p>
</div>
{% if recommendations.total > 0 %}
<div class="modal-body">
	<p class="modal-info">Produkty, które również mogą Cię zainteresować:</p>
    <ul class="thumbnails product-thumbnails">
    	{% for item in recommendations.rows %}
		<li class="span1">
	    	<div class="thumbnail">
	        	<div class="labels">
	            	{% if ( item.discountprice != NULL and item.discountprice != item.price ) %}
		        	<span class="label label-promotion">{% trans %}TXT_PROMOTION{% endtrans %}</span>
		        	{% endif %}
		        	{% if item.new == 1 %}
		        	<span class="label label-new">{% trans %}New product{% endtrans %}</span>
		        	{% endif %}
				</div>
	            <a href="{{ path('frontend.productcart', {"param": item.seo}) }}"><img src="{{ item.photo }}" alt="{{ item.name }}"></a>
	            <div class="caption">
	            	<a href="{{ path('frontend.productcart', {"param": item.seo}) }}"><h4>{{ item.name }}</h4></a>
	                {% if item.discountprice > 0 %}
					<p class="price promo">{% if showtax == 0 %}{{ item.discountpricenetto|priceFormat }}{% else %}{{ item.discountprice|priceFormat }}{% endif %} <small>{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</small></p>
				    {% else %}
				    <p class="price">{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</p>
				    {% endif %}
	                <p class="action">
	                	<a class="btn btn-primary" onclick="xajax_doQuickAddCart({{ item.id }});return false;" href="{{ path('frontend.productcart', {"param": item.seo}) }}"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_ADD_TO_CART{% endtrans %}</a>
	                </p>
				</div>
			</div>
		</li>
		{% endfor %}
	</ul>
</div>
{% endif %}
<div class="modal-footer form-actions-clean">
	<a href="#" class="btn  pull-left" data-dismiss="modal"><i class="icon-arrow-left"></i> Kontynuuj zakupy</a>
	<a href="{{ path('frontend.cart') }}" class="btn btn-inverse"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_GOTO_CART{% endtrans %}</a>
</div>

