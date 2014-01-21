{% extends "layoutbox.tpl" %}
{% block content %}
{% if draft is defined %}
<div class="alert alert-info">
	{{ draft }}
</div>
{% endif %}
<article id="productInfo" class="article marginbt20" itemscope itemtype="http://schema.org/Product">
	<div class="row-fluid row-form">
    	<div class="span9">
        	<div class="row-fluid">
            	<div class="span6">
                	<ul class="pagination">
                		{% if product.previous != NULL %}
                    	<li><a href="{{ path('frontend.productcart', {"param": product.previous}) }}">← Poprzedni produkt</a></li>
                    	{% endif %}
                    	{% if product.next != NULL %}
                        <li><a href="{{ path('frontend.productcart', {"param": product.next}) }}">Kolejny produkt →</a></li>
                        {% endif %}
                    </ul>
                    <div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
	                    <div class="image-large">
	                    	<a href="{{ product.mainphoto.large }}" rel="gallery">
			                	<img src="{{ product.mainphoto.normal }}" alt="{{ product.productname }}" itemprop="image">
			                </a>
	                    </div>
	                    {% if product.otherphoto.small|length > 0%}
	                    <a href="#" class="slider-move slider-moveLeft"></a>
	                    <div class="image-slider">
							<ul>
								<li><a href="{{ product.mainphoto.large }}" title="" rel="gallery"><img src="{{ product.mainphoto.normal }}" alt="" ></a></li>
								{% for key, otherphoto in product.otherphoto.small %}
								<li><a href="{{ product.otherphoto.large[key] }}" title="" rel="gallery"><img src="{{ otherphoto }}" alt="" ></a></li>
								{% endfor %}
							</ul>
						</div>
	                    <a href="#" class="slider-move slider-moveRight"></a>
	                    {% endif %}
                    </div>
				</div>
                <div class="span6">
                	<h1 class="product-name" itemprop="name">{{ product.productname }}</h1>
                    <ul class="labels unstyled">
                    	{% if product.discountprice != NULL %}
                    	<li class="label-promotion">{% trans %}TXT_PROMOTION{% endtrans %}</li>
                    	{% endif %}
                    	{% if product.new == 1 %}
                    	<li class="label-new">{% trans %}New product{% endtrans %}</li>
                    	{% endif %}
                    	{% for status in product.statuses %}
                        <li class="label{% if status.symbol != '' %}-{{ status.symbol }}{% endif %}">{{ status.name }}</li>
                        {% endfor %}
                    </ul>
                    {% if product.opinions > 0 %}
					<div class="product-star" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
						<div class="star pull-left readonly" itemprop="ratingValue" data-rating="{{ product.rating }}">{{ product.rating }}</div>
                        <span class="info pull-left">(<strong>{{ product.rating }}</strong>/5) <a href="#review" title="">{% trans %}TXT_OPINION{% endtrans %} (<span itemprop="reviewCount">{{ product.opinions }}</span>)</a></span>
					</div>
					{% endif %}
                    <div class="intro">
                    	<div itemprop="description">{{ product.shortdescription }}</div>
                    	{% if product.producername != '' %}
                        <p itemprop="brand">{% trans %}TXT_PRODUCER{% endtrans %}: <a href="{{ path('frontend.producerlist', {"param": product.producerurl}) }}" title="{{ product.producername }}"><strong>{{ product.producername }}</strong></a></p>
                        	{% if product.collectionname != '' %}
                        	<p itemprop="brand">{% trans %}TXT_COLLECTION{% endtrans %}: <a href="{{ path('frontend.producerlist', {"param": product.producerurl, "collection": product.collectionseo}) }}" title="{{ product.collectionname }}"><strong>{{ product.collectionname }}</strong></a></p>
                        	{% endif %}
                        {% endif %}
                        <p id="intro-links">
							{% if product.description != '' %}
                        	<a href="#product-description" title="">Opis szczegółowy</a><br>
							{% endif %}
                        	{% if technicalData|length > 0 %}
                        	<a href="#technical-data" title="">Parametry techniczne</a><br>
                        	{% endif %}
                        	<a href="#reviews" title="">{% trans %}TXT_OPINION{% endtrans %}</a><br>
                        </p>
					</div>
					<a target="_blank" style="position: relative;bottom: -22px;" href="http://pinterest.com/pin/create/button/?url={{ path('frontend.productcart', {"param": product.seo}) }}&media={{ product.mainphoto.orginal }}" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
					<div style="position: relative;bottom: -32px;" class="fb-like" data-send="true" data-width="360" data-show-faces="false" data-font="arial"></div>
				</div>
			</div>
		</div>
        <div class="span3">
        	<div id="addToCart" class="well well-small" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        		{% if ( product.discountprice != NULL and product.discountprice != product.price ) %}
        			<span class="price price-large" id="changeprice" itemprop="price">{% if showtax == 0 %}{{ product.discountpricenetto|priceFormat }}{% else %}{{ product.discountprice|priceFormat }}{% endif %}</span>
                	<span class="price price-small" id="changeprice-old">{% if showtax == 0 %}{{ product.pricenetto|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}</span>
				{% else %}
					<span class="price price-large" id="changeprice" itemprop="price">{% if showtax == 0 %}{{ product.pricewithoutvat|priceFormat }}{% else %}{{ product.price|priceFormat }}{% endif %}</span>
				{% endif %}
            	<div class="hr"></div>
                <ul>
                	{% if product.trackstock == 1 %}
                	<li><span>{% trans %}TXT_PRODUCT_IS_AVAILABLE{% endtrans %} <strong class="green available">{% trans %}TXT_AVAILABLE{% endtrans %}</strong><strong class="noavailable red">{% trans %}TXT_NOT_AVAILABLE{% endtrans %}</strong></span></li>
                	{% else %}
                	<li><span>{% trans %}TXT_PRODUCT_IS_AVAILABLE{% endtrans %} <span class="green"><strong>dostępny</strong></span></span></li>
                	{% endif %}
                	
                	{% if delivery|length > 0 %}
                    <li><span>{% trans %}TXT_DISPATCH{% endtrans %}: <strong>{% trans %}TXT_FROM{% endtrans %} {{ deliverymin|priceFormat }}</strong></span></li>
                    {% endif %}
                    
                    {% if product.availablityname != '' %}
					<li><span>Dostawa w ciągu <strong id="availablity">{{ product.availablityname }}</strong></span></li>
					{% endif %}
				</ul>
                <div class="hr"></div>
                <form>
					<input type="hidden" id="attributevariants" value="0" />
					<input type="hidden" id="availablestock" value="{{ product.stock }}" />
					<input type="hidden" id="variantprice" value="{{ product.price }}" />
					{% if attset != NULL %}
					{% for grid, attributesgroup in attributes %}
					<label>{{ attributesgroup.name }}:</label>
                	<select id="attributes-{{ grid }}" name="attributes-{{ grid }}" class="attributes span12">
                    	{% for v, variant in attributesgroup.attributes %}
	        			<option value="{{ v }}" {% if v == attributesgroup.primary %}selected="selected"{% endif %}>{{ variant }}</option>
	        			{% endfor %}
                    </select>
                    {% endfor %}
                    <div class="hr"></div>
                    {% endif %}
                    <div class="amount">
                    	<div class="pull-left mgr5">{% trans %}TXT_QUANTITY{% endtrans %}:</div>
                        <div class="pull-left mgr5"><input type="text" class="spinnerhide" data-packagesize="{{ product.packagesize }}" id="product-qty" value="{{ product.packagesize }}"></div>
                        <div class="pull-left">{{ product.unit }}</div>
						<div class="clearfix"></div>
					</div>
                    <button type="submit" id="add-cart" class="btn btn-primary btn-large available"><i class="icon-shopping-cart icon-white"></i> {% trans %}TXT_ADD_TO_CART{% endtrans %}</button>
                    <a href="{{ path('frontend.contact', {"param": product.idproduct}) }}" class="btn btn-large btn-danger noavailable"><i class="icon-question-sign icon-white"></i> {% trans %}TXT_REQUEST_QUOTE{% endtrans %}</a>
					<div class="btn-group">
						<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
					    	Opcje dodatkowe
					    	<span class="caret"></span>
					  	</a>
					  	<ul class="dropdown-menu">
					    	<li><a href="" id="addtoclip"><i class="icon-plus"></i> {% trans %}TXT_ADD_TO_CLIPBOARD{% endtrans %}</a></li>
					    	{% if compareproductsids|length > 0 %}
					    	<li><a href="#" onclick="window.location.href = '{{ path('frontend.productcompare') }}';"><i class="icon-eye-open"></i> {% trans %}TXT_COMPARE_PRODUCTS{% endtrans %}</a></li>
					    	{% endif %}
					    	{% if product.idproduct not in compareproductsids %}
					    	<li><a href="#" onclick="xajax_addProductToCompare({{ product.idproduct }});"><i class="icon-plus"></i> {% trans %}TXT_ADD_TO_COMPARE{% endtrans %}</a></li>
					    	{% else %}
					    	<li><a href="#" onclick="xajax_deleteProductFromCompare({{ product.idproduct }});"><i class="icon-remove"></i> {% trans %}TXT_DELETE_FROM_COMPARE{% endtrans %}</a></li>
					    	{% endif %}
					  	</ul>
					</div>
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
		bTrackStock: producttrackstock,
	});
	{% else %}
	if(producttrackstock == 1 && ($('#availablestock').val() == 0)){
		$('.available').hide();
		$('.noavailable').show();
	}else{
		$('.available').show();
		$('.noavailable').hide();
	}
	{% endif %}

	$('#addtoclip').unbind('click').bind('click', function(){
		return xajax_addProductToWishList({{ product.idproduct }}, $('#attributevariants').val());
	});
});

</script>
{% endblock %}