{% include 'cartbox/index/combinations.tpl' %}
	
{% for product in productCart if product.combinationid is null %}
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
		<td>
			{% if product.idproduct != giftwrap.product.idproduct %}
			<input type="text" class="product-quantity spinnerhide" data-value="{{ product.qty }}" data-packagesize="{{ product.packagesize }}" value="{{ product.qty }}" onchange="$(this).spinner('disable');xajax_changeQuantity({{ product.idproduct }},null,this.value);" />
			{% else %}
			{{ product.qty }}
			{% endif %}
		</td>
		<td>{{ product.qtyprice|priceFormat }}</td>
		<td><a class="btn btn-mini" href="#" onclick="xajax_deleteProductFromCart({{ product.idproduct }}, null); return false;"><i class="icon-remove"></i></a></td>
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
		<td><input type="text" class="product-quantity spinnerhide" data-value="{{ attribprod.qty }}" data-packagesize="{{ attribprod.packagesize }}" value="{{ attribprod.qty }}" onchange="$(this).spinner('disable');xajax_changeQuantity({{ attribprod.idproduct }},{{ attribprod.attr }},this.value);" /></td>
		<td>{{ attribprod.qtyprice|priceFormat }}</td>
		<td><a class="btn btn-mini" href="#" onclick="xajax_deleteProductFromCart({{ attribprod.idproduct }}, {{ attribprod.attr }}); return false;"><i class="icon-remove"></i></a></td>
	</tr>
	{% endfor %}
{% endfor %}	

<script type="text/javascript">
function restoreQty(){
	$('.product-quantity').each(function(){
		$(this).val($(this).attr('data-value'));
	});
}
</script>