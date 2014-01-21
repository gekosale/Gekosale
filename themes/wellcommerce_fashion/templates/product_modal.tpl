<button class="close" data-dismiss="modal">&times;</button>
<div class="modal-body">
	<input type="hidden" id="attributevariants" value="0" />
	<input type="hidden" id="availablestock" value="{{ product.stock }}" />
	<input type="hidden" id="variantprice" value="{% if showtax == 0 %}{{ product.pricenetto|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}" />
	<h1>{{ product.productname }}</h1>
	<div class="image-large ">
		<img src="{{ product.mainphoto.normal }}" alt="{{ product.productname }}">
	</div>
	<div id="addToCart" class="well well-small">
		{% if ( product.discountprice != NULL and product.discountprice != product.price ) %}
        <span class="price price-large" id="changeprice">{% if showtax == 0 %}{{ product.discountpricenetto|priceFormat }}{% else %}{{ product.discountprice|priceFormat }}{% endif %}</span>
        <span class="price price-small" id="changeprice-old">{% if showtax == 0 %}{{ product.pricenetto|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}</span>
		{% else %}
		<span class="price price-large" id="changeprice">{% if showtax == 0 %}{{ product.pricenetto|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}</span>
		{% endif %}
		<div class="hr"></div>
		<ul>
			{% if product.trackstock == 1 %}
            <li><span>{% trans %}TXT_PRODUCT_IS_AVAILABLE{% endtrans %} <strong class="green available">{% trans %}TXT_AVAILABLE{% endtrans %}</strong><strong class="noavailable red">{% trans %}TXT_NOT_AVAILABLE{% endtrans %}</strong></span></li>
            {% else %}
            <li><span>{% trans %}TXT_PRODUCT_IS_AVAILABLE{% endtrans %} <span class="green"><strong>dostępny</strong></span></span></li>
            {% endif %}
			<li><span>{% trans %}TXT_DISPATCH{% endtrans %}: <strong>{% trans %}TXT_FROM{% endtrans %} {{ deliverymin|priceFormat }}</strong></span></li>
			{% if product.availablityname != '' %}
			<li><span>Dostawa w ciągu <strong id="availablity">{{ product.availablityname }}</strong></span></li>
			{% endif %}
		</ul>
		<div class="hr"></div>
			{% if attset != NULL %}
			{% for attributesgroup in attributes %}
			<label>{{ attributesgroup.name }}:</label>
            <select id="{{ grid }}" name="{{ grid }}" class="attributes span2">
            {% for v, variant in attributesgroup.attributes %}
	        	<option value="{{ v }}"  {% if v == attributesgroup.primary %}selected="selected"{% endif %}>{{ variant }}</option>
	        {% endfor %}
            </select>
            {% endfor %}
			<div class="hr"></div>
			{% endif %}
			<div class="amount">
                <div class="pull-left mgr5">{% trans %}TXT_QUANTITY{% endtrans %}:</div>
                <div class="pull-left mgr5"><input type="text" class="spinnerhide pull-left" id="product-qty" data-packagesize="{{ product.packagesize }}" id="product-qty" value="{{ product.packagesize }}"></div>
                <div class="pull-left">{{ product.unit }}</div>
				<div class="clearfix"></div>
			</div>
			<button type="submit" onclick="$('#productModal').modal('hide');xajax_addProductToCart({{ product.idproduct }}, $('#attributevariants').val(), $('#product-qty').val());return false;" class="btn btn-primary btn-large available marginbt10" id="add-cart"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_ADD_TO_CART{% endtrans %} </button>
			<a href="{{ path('frontend.contact', {"param": product.idproduct}) }}" class="btn btn-large btn-danger noavailable"><i class="icon-question-sign icon-white"></i> {% trans %}TXT_REQUEST_QUOTE{% endtrans %}</a>
			<button type="submit" class="btn" id="addtoclip" onclick="xajax_addProductToWishList({{ product.idproduct }}, $('#attributevariants').val()); return false;">{% trans %}TXT_ADD_TO_CLIPBOARD{% endtrans %}</button>
	</div>
</div>