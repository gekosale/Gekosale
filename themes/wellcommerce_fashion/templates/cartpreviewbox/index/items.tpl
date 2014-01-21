{% if productCart|length > 0 %} 
<ul>
	{% for product in productCart %}
	<li>
		<h4>
		{% if product.standard == 1 %}
			<a href="{{ path('frontend.productcart', {"param": product.seo}) }}">
				<span class="name">{{ product.name }}</span>
				<span class="price">{price}{{ product.qtyprice }}{/price}</span>
			</a>
		{% endif %}
		{% if product.attributes != NULL %}
			{% for attribprod in product.attributes %}
			<a href="{{ path('frontend.productcart', {"param": attribprod.seo}) }}">
				<span class="name">{{ attribprod.name }}</span>
				<span class="price">{price}{{ attribprod.qtyprice }}{/price}</span>
			</a>
			{% endfor %}
		{% endif %}
		</h4>
	</li>
	{% endfor %}
</ul>
{% else %}
<p class="empty">{% trans %}TXT_EMPTY_CART{% endtrans %}</p>
{% endif %}
			
{% if productCart|length > 0 %} 
<dl>
	<dt>{% trans %}TXT_PRODUCTS_ON_CART{% endtrans %}:</dt><dd>{{ count }} {% trans %}TXT_QTY{% endtrans %}</dd>
	<dt>{% trans %}TXT_PRODUCT_SUBTOTAL{% endtrans %}:</dt><dd>{price}{{ globalPrice }}{/price}</dd>
</dl>
<p class="place-order">
	<a href="{{ path('frontend.cart') }}" class="button-red">{% trans %}TXT_SHOW_CART{% endtrans %}</a><br/>
</p>
{% endif %}