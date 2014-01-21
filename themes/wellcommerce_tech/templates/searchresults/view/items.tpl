{% if items|length > 0 %}
<div style="z-index: 900000; width: 450px;right: 289px;" class="layout-box-type-product-list layout-box-width-class-2 layout-box"> 
<div class="layout-box-content" style="margin-top: 0px">
						
<ul class="list-long"> 
{% for item in items %}
	{% if item.discountprice > 0 %}
	<li class="promo">
		<h4>
			<a href="{{ path('frontend.productcart') }}/{{ item.seo }}" title="{{ item.name }}">
				<span class="image">
				<img class="promo" src="{{ DESIGNPATH }}_images_frontend/core/icons/product-promo.png" alt="Promocja!" title="Promocja!"/>
				<img src="{{ item.photo }}" alt="{{ item.name }}"/>
				</span>
				<span class="name">{{ item.name }}</span>
				{% if showtax == 0 %}
					<span class="price"><ins>{{ item.discountpricenetto }}</ins> <del>{{ item.pricenetto }}</del></span>
				{% else %}
					<span class="price"><ins>{{ item.discountprice }}</ins> <del>{{ item.price }}</del></span>
				{% endif %}		
			</a>
		</h4>
		<div class="description">
			{{ item.shortdescription }}
		</div>
	</li>
	{% else %}
	<li>
		<h4>
			<a href="{{ path('frontend.productcart') }}/{{ item.seo }}" title="{{ item.name }}">
				<span class="image"><img src="{{ item.photo }}" alt="{{ item.name }}"/></span>
				<span class="name">{{ item.name }}</span>
				{% if showtax == 0 %}
					<span class="price">{{ item.pricenetto }}</span>
				{% else %}
					<span class="price">{{ item.price }}</span>
				{% endif %}		
			</a>
		</h4>
		<div class="description">
			{{ item.shortdescription }}
		</div>
	</li>
	{% endif %}
{% endfor %} 
</ul> 
							
<p class="see-more" style="margin-top: 5px;margin-right: 10px;float: right;"><a href="{{ path('frontend.productsearch') }}/{{ phrase }}">Zobacz wszystkie</a></p> 
</div>
</div>
</div>
{% else %}
<div class="layout-box" style="z-index: 900000; width: 450px;right: 305px;">
	<div class="layout-box-content">
		<p>{% trans %}ERR_EMPTY_PRODUCT_SEARCH{% endtrans %}</p>
	</div>
{% endif %}