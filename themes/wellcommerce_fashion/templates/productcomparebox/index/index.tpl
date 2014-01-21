<div class="box-comparison" id="products-comparison">
    <ul class="reset clearfix">
		{% for product in compareproducts %}
		<li>
			<figure><img src="{{ product.photo }}" alt="" style="width: 25px;"></figure>
			<a class="delete" href="#" title="" onclick="return xajax_deleteProductFromCompare({{ product.id }});">Usuń</a>
		</li>
		{% endfor %}
	</ul>
	<a class="button" href="{{ path('frontend.productcompare') }}" title="">Porównaj</a>
</div>
