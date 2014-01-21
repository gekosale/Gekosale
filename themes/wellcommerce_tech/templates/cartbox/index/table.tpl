<table class="table table-striped table-bordered products-table">
	<thead>
		<tr>
			<th style="width: 88px;"></th>
			<th>{% trans %}TXT_PRODUCT_NAME{% endtrans %}</th>
			<th style="width: 80px">{% trans %}TXT_PRICE{% endtrans %}</th>
			<th style="width: 85px">{% trans %}TXT_PRODUCT_QUANTITY{% endtrans %}</th>
			<th style="width: 100px">{% trans %}TXT_PRODUCT_SUBTOTAL{% endtrans %}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{% include 'cartbox/index/products.tpl' %}
	</tbody>
	<tfoot>
		{% include 'cartbox/index/coupons.tpl' %}
		{% include 'cartbox/index/methods.tpl' %}
		{% include 'cartbox/index/discounts.tpl' %}
		{% include 'cartbox/index/giftwrap.tpl' %}
		{% include 'cartbox/index/summary.tpl' %}
	</tfoot>
</table>

{% if minimumordervalue > 0 %}
<div class="alert alert-block alert-error">
	<h4>
		<strong>Minimalna wartość zamówienia nie została osiągnięta</strong><br />
		Aby złożyć zamówienie, musisz jeszcze dodać do koszyka produkty o wartości <strong>{{ minimumordervalue|priceFormat }}</strong>
	</h4>
</div>
{% else %}
<div class="pull-right">
	<a href="{{ path('frontend.home') }}" title=""><i class="icon icon-arrow-left-blue"></i> {% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</a>
	{% if payments|length and deliverymethods|length %}
	<a href="{{ path('frontend.checkout') }}" class="btn btn-large btn-primary marginlt20">{% trans %}TXT_PLACE_ORDER{% endtrans %}</a>
	{% endif %}
</div>
{% endif %}
