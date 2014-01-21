<h1>{{ SHOP_NAME }}</h1>
<h1>{{ product.productname }}</h1>
<a href="{{ path('frontend.productcart', {"param": product.seo}) }}">{{ path('frontend.productcart', {"param": product.seo}) }}</a>
	<div class="product-photos">
		{% if product.mainphoto.normal is defined %}
		<img class="mainphoto" src="{{ product.photo.normal[0] }}">
		{% endif %}
	</div>
    <table border="0" cellpadding="3" cellspacing="3" width="400">
    	{% if product.producername != '' %}    
    	<tr>
	   		<td>{% trans %}TXT_PRODUCER{% endtrans %}</td>
	   		<td>{{ product.producername }}</td>
	   	</tr>
	   	{% endif %}       
	   	<tr>
	   		<td>{% trans %}TXT_PRICE{% endtrans %}</td>
	   		<td>{% if product.discountprice != NULL %}
			{{ product.discountprice|priceFormat }}
		{% else %}
			{{ product.price|priceFormat }}
		{% endif %}</td>
	   	</tr>
	   	<tr>
	   		<td>{% trans %}TXT_NET_PRICE{% endtrans %}</td>
	   		<td>{% if product.discountprice != NULL %}
			{{ product.discountpricenetto|priceFormat }}
		{% else %}
			{{ product.pricewithoutvat|priceFormat }}
		{% endif %}</td>
	   	</tr>
	</table> 
    {{ product.description }}
    {% for technical in technicalData %} 
    <table border="1" cellpadding="3" cellspacing="3">
	   	<tr>
	   		<td colspan="2" style="text-align: center">{{ technical.name }}</td>
	   	</tr>
		{% for attribute in technical.attributes %}
		{% if attribute.value != '' %}
	   	<tr>
	   		<td>{{ attribute.name }}</td>
	   		<td>{{ attribute.value }}</td>
	   	</tr>
		{% endif %}
	   	{% endfor %}
	</table>
	{% endfor %}
        
