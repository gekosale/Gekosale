{% if product.description != '' %} 
<div id="product-description" class="well well-clean well-small product-details" itemprop="description">
	<h2>{% trans %}TXT_DESCRIPTION{% endtrans %}</h2>
	{{ product.description }}
</div>
{% endif %}
{% if product.longdescription != '' %} 
<div class="well well-clean well-small product-details">
	<h2>{% trans %}TXT_ADDITIONAL_INFO{% endtrans %}</h2>
	{{ product.longdescription }}
</div>
{% endif %}

{% if delivery|length > 0 %}
<div class="well well-clean well-small product-details">
	<h2>{% trans %}TXT_COST_OF_DELIVERY{% endtrans %}</h2>
	<table class="table">
		<tbody>
			{% for d in delivery %}
			<tr>
				<td>{{ d.name }}</td>
				<th>{{ d.dispatchmethodcost|priceFormat }}</th>
			</tr>
			{% endfor %}
		</tbody>
	</table>
</div>
{% endif %}

{% if files|length > 0 %}
<div class="well well-clean well-small product-details" id="files">
	{% include 'productbox/index/files.tpl' %}
</div>
{% endif %}

{% if technicalData|length > 0 %}
<div id="technical-data" class="well well-clean well-small product-details">
	{% include 'productbox/index/technicaldata.tpl' %}
</div>
{% endif %}
<div id="reviews">
{% include 'productbox/index/opinions.tpl' %}
</div>
