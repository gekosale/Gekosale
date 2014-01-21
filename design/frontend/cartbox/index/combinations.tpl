{% for combination in productCartCombinations %} {% for product in
productCart if product.combinationid == combination.id %} {% if
loop.first %} {% if product.standard == 1 %}
<tr>
	<td><img src="{{ product.smallphoto }}" alt=""></td>
	<td><a href="{{ path('frontend.productcart', {" param":product.seo}) }}">{{
			product.qty }} x {{ product.name }}</a></td>
	<td rowspan="{{ combination.products|length }}"><span class="red">{{
			(combination.summary.totalDiscountPriceGross /
			combination.currentqty)|priceFormat }}</span> <span class="old">{{
			(combination.summary.totalStandardPriceGross /
			combination.currentqty)|priceFormat }}</span></td>
	<td rowspan="{{ combination.products|length }}"><input type="text"
		class="product-quantity spinnerhide"
		data-value="{{ combination.currentqty }}" data-packagesize="1"
		value="{{ combination.currentqty }}"
		onchange="$(this).spinner('disable');doChangeAJAXCombinationQty({{ combination.id }},this.value);" /></td>
	<td rowspan="{{ combination.products|length }}"><span class="red">{{
			combination.summary.totalDiscountPriceGross|priceFormat }}</span> <span
		class="old">{{ combination.summary.totalStandardPriceGross|priceFormat
			}}</span></td>
	<td rowspan="{{ combination.products|length }}"><a class="btn btn-mini"
		href="#"
		onclick="xajax_deleteCombinationFromCart({{ combination.id }}); return false;"><i
			class="icon-remove"></i></a></td>
</tr>
{% endif %} {% for attribprod in product.attributes if
product.attributes != NULL %}
<tr>
	<td><img src="{{ attribprod.smallphoto }}" alt=""></td>
	<td><a href="{{ path('frontend.productcart', {" param":attribprod.seo}) }}">{{
			attribprod.qty }} x {{ attribprod.name }}</a> {% for feature in
		attribprod.features %} <br />{{ feature.group }}: {{
		feature.attributename }} {% endfor %}</td>
	<td rowspan="{{ combination.products|length }}"><span class="red">{{
			(combination.summary.totalDiscountPriceGross /
			combination.currentqty)|priceFormat }}</span> <span class="old">{{
			(combination.summary.totalStandardPriceGross /
			combination.currentqty)|priceFormat }}</span></td>
	<td rowspan="{{ combination.products|length }}"><input type="text"
		class="product-quantity spinnerhide"
		data-value="{{ combination.currentqty }}" data-packagesize="1"
		value="{{ combination.currentqty }}"
		onchange="$(this).spinner('disable');xajax_changeCombinationQuantity({{ combination.id }},this.value);" /></td>
	<td rowspan="{{ combination.products|length }}"><span class="red">{{
			combination.summary.totalDiscountPriceGross|priceFormat }}</span> <span
		class="old">{{ combination.summary.totalStandardPriceGross|priceFormat
			}}</span></td>
	<td rowspan="{{ combination.products|length }}"><a class="btn btn-mini"
		href="#"
		onclick="xajax_deleteCombinationFromCart({{ combination.id }}); return false;"><i
			class="icon-remove"></i></a></td>
</tr>
{% endfor %} {% else %} {% if product.standard == 1 %}
<tr>
	<td><img src="{{ product.smallphoto }}" alt=""></td>
	<td><a href="{{ path('frontend.productcart', {" param":product.seo}) }}">{{
			product.qty }} x {{ product.name }}</a></td>
</tr>
{% endif %} {% for attribprod in product.attributes if
product.attributes != NULL %}
<tr>
	<td><img src="{{ attribprod.smallphoto }}" alt=""></td>
	<td><a href="{{ path('frontend.productcart', {" param":attribprod.seo}) }}">{{
			attribprod.qty }} x {{ attribprod.name }}</a> {% for feature in
		attribprod.features %} <br />{{ feature.group }}: {{
		feature.attributename }} {% endfor %}</td>
</tr>
{% endfor %} {% endif %} {% endfor %} {% endfor %}
