{% for product in productCart %}
	{% if product.standard == 1 %}
	<tr>
		<td><img src="{{ product.smallphoto }}" alt=""></td>
		<td><a href="{{ path('frontend.productcart', {"param": product.seo}) }}">{{ product.name }}</a></td>
		<td>
		{% if product.pricebeforepromotiongross is not null %}
			<span class="red">{{ product.newprice|priceFormat }}</span>
			<span class="old">{{ product.pricebeforepromotiongross|priceFormat }}</span>
		{% else %}
			{{ product.newprice|priceFormat }}
		{% endif %}
		</td>
		<td>{{ product.qty }}</td>
		<td>{{ product.qtyprice|priceFormat }}</td>
	</tr>
	{% endif %}
	
	{% for attribprod in product.attributes if product.attributes != NULL %}
	<tr>
		<td><img src="{{ attribprod.smallphoto }}" alt=""></td>
		<td><a href="{{ path('frontend.productcart', {"param": attribprod.seo}) }}">{{ attribprod.name }}</a>
		{% for feature in attribprod.features %}
			<br />{{ feature.group }}: {{ feature.attributename }}
		{% endfor %}
		</td>
		<td>
		{% if attribprod.pricebeforepromotiongross is not null %}
			<span class="red">{{ attribprod.newprice|priceFormat }}</span>
			<span class="old">{{ attribprod.pricebeforepromotiongross|priceFormat }}</span>
		{% else %}
			{{ attribprod.newprice|priceFormat }}
		{% endif %}
		</td>
		<td>{{ attribprod.qty }}</td>
		<td>{{ attribprod.qtyprice|priceFormat }}</td>
	</tr>
	{% endfor %}
{% endfor %}	