{% if productCart|length > 0 %} 
<a href="{{ path('frontend.cart') }}" class="inherit">
<i class="icon icon-chevron-down"></i>
{% endif %}
<p>{% trans %}TXT_NUMBEROFITEM{% endtrans %}: <span>{{ count }}</span></p>
<p>{% trans %}TXT_PRODUCT_SUBTOTAL{% endtrans %}: <span>{{ globalPrice|priceFormat }}</span></p>
{% if productCart|length > 0 %} 
</a>
<div id="topBasketContent" class="span4">
	{% if freeshipping is defined %}
		{% if freeshipping > 0 %}
		<span class="info info-green">Do darmowej wysyłki brakuje <strong>{{ freeshipping|priceFormat }}</strong></span>
		{% else %}
		<span class="info info-green">Kwota zakupów pozwala na darmową wysyłkę!</strong></span>
		{% endif %}
	{% endif %}
    <table class="table">
    	<tbody>
    	{% for product in productCart %}
    		{% if product.standard == 1 %}
        	<tr>
                <td style="width: 120px;"><a href="{{ path('frontend.productcart', {"param": product.seo}) }}">{{ product.name }}</a></td>
                <td style="vertical-align: middle;">{{ product.qty }} {% trans %}TXT_QTY{% endtrans %}</td>
                <td><strong>{{ product.qtyprice|priceFormat }}</strong><a href="#" class="btn remove" onclick="xajax_deleteProductFromCart({{ product.idproduct }}, null); return false;"><i class="icon-remove"></i></a></td>
			</tr>
			{% endif %}
			{% if product.attributes != NULL %}
				{% for attribprod in product.attributes %}
				<tr>
	                <td style="width: 120px;"><a href="{{ path('frontend.productcart', {"param": attribprod.seo}) }}">{{ attribprod.name }}</a>
	                {% for feature in attribprod.features %}
						<br />{{ feature.group }}: {{ feature.attributename }}
					{% endfor %}
	                </td>
	                <td style="vertical-align: middle;">{{ attribprod.qty }} {% trans %}TXT_QTY{% endtrans %}</td>
	                <td><strong>{{ attribprod.qtyprice|priceFormat }}</strong><a href="#" class="btn remove" onclick="xajax_deleteProductFromCart({{ attribprod.idproduct }}, {{ attribprod.attr }}); return false;"><i class="icon-remove"></i></a></td>
				</tr>
				{% endfor %}
			{% endif %}
		{% endfor %}
		</tbody>
	</table>
	<a href="{{ path('frontend.cart') }}" class="btn-basket pull-left font">{% trans %}TXT_CHECKOUT{% endtrans %}</a>
	<h3 class="pull-right"><small>{% trans %}TXT_PRODUCT_SUBTOTAL{% endtrans %}:</small> {{ globalPrice|priceFormat }}</h3>
</div>
{% endif %}