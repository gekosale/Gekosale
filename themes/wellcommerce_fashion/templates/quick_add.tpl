<article id="productInfo" class="article marginbt20">
	<div class="row-fluid row-form">
    	<div class="span8">
        	<div class="row-fluid">
            	<div class="span3">
                    	<a href="{{ product.mainphoto.orginal }}">
		                	<img src="{{ product.mainphoto.normal }}" alt="{{ product.productname }}">
		                </a>
                    {% if product.otherphoto.small|length > 0%}
                    <a href="#" class="slider-move slider-moveLeft"></a>
                    <div class="image-slider">
						<ul>
							<li><a href="{{ product.mainphoto.orginal }}" title=""><img src="{{ product.mainphoto.normal }}" alt=""></a></li>
							{% for key, otherphoto in product.otherphoto.small %}
							<li><a href="{{ product.otherphoto.orginal[key] }}" title=""><img src="{{ otherphoto }}" alt=""></a></li>
							{% endfor %}
						</ul>
					</div>
                    <a href="#" class="slider-move slider-moveRight"></a>
                    {% endif %}
				</div>
                <div class="span9">
                	<h1 class="product-name">{{ product.productname }}</h1>
                    <ul class="labels unstyled">
                    	{% if product.discountprice != NULL %}
                    	<li class="label-promotion">{% trans %}TXT_PROMOTION{% endtrans %}</li>
                    	{% endif %}
                    	{% if product.new == 1 %}
                    	<li class="label-new">{% trans %}New product{% endtrans %}</li>
                    	{% endif %}
                    	{% for status in product.statuses %}
                        <li class="label-{{ status.symbol }}">{{ status.name }}</li>
                        {% endfor %}
                    </ul>
                    {% if product.opinions > 0 %}
					<div class="product-star">
						<div class="star pull-left readonly" data-rating="{{ product.rating }}"></div>
                        <span class="info pull-left">(<strong>{{ product.rating }}</strong>/5) <a href="#review" title="">{% trans %}TXT_OPINION{% endtrans %} ({{ product.opinions }})</a></span>
					</div>
					{% endif %}
                    <div class="intro">
                    	{{ product.shortdescription }}
                    	{% if product.producername != '' %}
                        <p>{% trans %}TXT_PRODUCER{% endtrans %}: <a href="{{ path('frontend.producerlist', {"param": product.producerurl}) }}" title="{{ product.producername }}"><strong>{{ product.producername }}</strong></a></p>
                        {% endif %}
					</div>
				</div>
			</div>
		</div>
        <div class="span4">
        	<div id="addToCart" class="well well-small">
        		{% if ( item.discountprice != NULL and item.discountprice != item.price ) %}
        			<span class="price price-large" id="changeprice">{% if showtax == 0 %}{{ product.discountpricenetto|priceFormat }}{% else %}{{ product.discountprice|priceFormat }}{% endif %}</span>
                	<span class="price price-small" id="changeprice-old">{% if showtax == 0 %}{{ product.pricenetto|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}</span>
				{% else %}
					<span class="price price-large" id="changeprice">{% if showtax == 0 %}{{ product.pricenetto|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}</span>
				{% endif %}
            	<div class="hr"></div>
                <ul>
                	{% if product.trackstock == 1 %}
                	<li><span>{% trans %}TXT_STOCK{% endtrans %}: <strong id="availbility">dostępny</strong></span></li>
                	{% else %}
                	<li><span>{% trans %}TXT_PRODUCT_IS_AVAILABLE{% endtrans %}: <span class="green"><strong>dostępny</strong></span></span></li>
                	{% endif %}
                    <li><span>{% trans %}TXT_DISPATCH{% endtrans %}: <strong>{% trans %}TXT_FROM{% endtrans %} {{ deliverymin|priceFormat }}</strong></span></li>
                    {% if product.availablityname != '' %}
					<li><span>Dostawa w ciągu <strong>{{ product.availablityname }}</strong></span></li>
					{% endif %}
				</ul>
                <div class="hr"></div>
                <form>
					<input type="hidden" id="attributevariants" value="0" />
					<input type="hidden" id="availablestock" value="{{ product.stock }}" />
					<input type="hidden" id="variantprice" value="{{ product.price }}" />
					{% if attset != NULL %}
					{% for attributesgroup in attributes %}
					<label>{{ attributesgroup.name }}:</label>
                	<select id="{{ grid }}" name="{{ grid }}" class="attributes span12">
                    	{% for v, variant in attributesgroup.attributes %}
	        			<option value="{{ v }}">{{ variant }}</option>
	        			{% endfor %}
                    </select>
                    {% endfor %}
                    <div class="hr"></div>
                    {% endif %}
                    <div class="amount">
                    	<div class="pull-left mgr5">{% trans %}TXT_QUANTITY{% endtrans %}:</div>
                        <div class="pull-left mgr5"><input type="text" class="spinnerhide" id="product-qty" value="1"></div>
                        <div class="pull-left">{{ product.unit }}</div>
						<div class="clearfix"></div>
					</div>
                    <button type="submit" id="add-cart" class="btn btn-primary btn-large available"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_ADD_TO_CART{% endtrans %}</button>
                    <a href="{{ path('frontend.contact', {"param": product.idproduct}) }}" class="btn btn-large btn-danger noavailable"><i class="icon-question-sign icon-white"></i> {% trans %}TXT_REQUEST_QUOTE{% endtrans %}</a>
				</form>
			</div>
		</div>
	</div>
</article>
<script type="text/javascript">

$(document).ready(function(){

	var producttrackstock = {{ product.trackstock }};

	$('#add-cart').unbind('click').bind('click', function(){
		if(producttrackstock == 1){
			if($('#availablestock').val() > 0){
				return xajax_addProductToCart({{ product.idproduct }}, $('#attributevariants').val(), $('#product-qty').val());
			}else{
				GError('{% trans %}ERR_SHORTAGE_OF_STOCK{% endtrans %}');
				return false;
			}
		}else{
			return xajax_addProductToCart({{ product.idproduct }}, $('#attributevariants').val(), $('#product-qty').val());
		}
	});

	{% if attset != NULL %}
	$('#productInfo').GProductAttributes({
		aoVariants: {{ variants }},
		bTrackStock: producttrackstock
	});
	{% else %}
	if(producttrackstock == 1 && ($('#availablestock').val() == 0)){
		$('#available').hide();
		$('#noavailable').show();
	}else{
		$('#available').show();
		$('#noavailable').hide();
	}
	{% endif %}

});

</script>