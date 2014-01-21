{% extends "layoutbox.tpl" %}
{% block content %}
<div class="head-block">
	<span class="font">{{ box.heading }}</span>
</div>

{% for item in combinationlist %}
<div class="product-list row-fluid">
	<div class="span9">
    	<ul class="thumbnails combination col5 product-thumbnails padding10">
    		{% for product in item.products %}
			<li class="span1">
    			<div class="thumbnail">
            		<a href="{{ path('frontend.productcart', {"param": product.seo}) }}" title="{{ product.productname }}">
            			<span class="photo">
            				<img src="{{ product.photo.small[0] }}" alt="{{ product.productname }}">
            			</span>
            		</a> 
            		<div class="caption">
	            		<h4>
	            			<a href="{{ path('frontend.productcart', {"param": product.seo}) }}" title="{{ product.productname }}">{{ product.numberofitems }} x {{ product.productname }}</a>
	            		</h4>
                		<p class="price promo">{{ product.totals.discountpricegross|priceFormat }} <small>{{ product.totals.standardpricegross|priceFormat }}</small></p>
					</div>
				</div>
			</li>
			{% if loop.last == false %}
			<li class="combination-separator">+</li>
			{% endif %}
			{% endfor %}
		</ul>
	</div>
	<div class="span3">
		<p class="price promo">{{ item.summary.totalDiscountPriceGross|priceFormat }}<small>{{ item.summary.totalStandardPriceGross|priceFormat }}</small></small></p>
        <p class="discount">Rabat: <strong>{{ item.discount }}%</strong></p>
        <p class="discount ">Oszczędzasz: <strong>{{ item.summary.totalDiscountGross|priceFormat }}</strong></p>
        <p class="action"><a href="#" class="btn btn-primary btn-large" onclick="xajax_doQuickAddCombinationCart({{ item.id }});return false;"><i class="icon-shopping-cart icon-white"></i> Kup ten zestaw</a></p>
	</div>
</div>
{% endfor %}
{% endblock %}