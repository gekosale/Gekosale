	<ul id="productTab" class="nav nav-tabs">
		{% if product.description != '' %}
		<li class="active"><a href="#product-description" data-toggle="tab">{% trans %}TXT_DESCRIPTION{% endtrans %}</a></li>
		{% endif %}
		{% if product.longdescription != '' %}
		<li><a href="#product-longdescription" data-toggle="tab">{% trans %}TXT_ADDITIONAL_INFO{% endtrans %}</a></li>
		{% endif %}
		{% if technicalData|length > 0 %}
		<li><a href="#technical-data" data-toggle="tab">{% trans %}TXT_SPECIFICATIONS{% endtrans %}</a></li>
		{% endif %}
		{% if files|length > 0 %}
		<li><a href="#files" data-toggle="tab">{% trans %}TXT_FILES{% endtrans %}</a></li>
		{% endif %}
		{% if delivery|length > 0 %}
		<li><a href="#deliverycost" data-toggle="tab">{% trans %}TXT_COST_OF_DELIVERY{% endtrans %}</a></li>
		{% endif %}
		<li><a href="#reviews" data-toggle="tab">{% trans %}TXT_OPINION{% endtrans %}</a></li>
	</ul>
	<div id="productTabContent" class="tab-content">
	{% if product.description != '' %}
	<div class="tab-pane fade active in product-details" id="product-description" itemprop="description">
		{{ product.description }}
	</div>
	{% endif %}
	{% if product.longdescription != '' %}
	<div class="tab-pane fade product-details" id="product-longdescription">
		{{ product.longdescription }}
	</div>
	{% endif %}
	{% if technicalData|length > 0 %}
	<div class="tab-pane fade product-details" id="technical-data">
		{% include 'productbox/index/technicaldata.tpl' %}
	</div>
	{% endif %}
	{% if files|length > 0 %}
	<div class="tab-pane fade product-details" id="files">
		{% include 'productbox/index/files.tpl' %}
	</div>
	{% endif %}
	{% if delivery|length > 0 %}
	<div class="tab-pane fade product-details" id="deliverycost">
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
	<div class="tab-pane fade product-details" id="reviews">
		{% include 'productbox/index/opinions.tpl' %}
	</div>
</div>
