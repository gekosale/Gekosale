{% if giftwrap.product is not empty %}
<tr>
	<td colspan="6">
		{% if giftwrap.active == 0 %}
		<div class="alert alert-block alert-info">
	    	<a href="#giftwrap" title="" class="btn btn-primary btn-large pull-right" data-toggle="collapse" data-target="#giftwrap"><i class="icon-heart icon-white"></i> Zapakuj jako prezent</a>
	        <h4><strong>Czy zapakować zamówienie jako prezent?</strong>
	        <br />Koszt opakowania ozdobnego to <strong>
	        	{% if giftwrap.product.discountprice > 0 %}
					{{ giftwrap.product.discountprice|priceFormat }}
				{% else %}
			   		{{ giftwrap.product.price|priceFormat }}
				{% endif %}</strong>.
			</h4>
		</div>
		{% endif %}
		<div class="collapse {% if giftwrap.active == 1 %}in{% endif %}" id="giftwrap">
			<h4>{{ giftwrap.product.productname }}</h4>
			<h4>{{ giftwrap.product.shortdescription }}</h4>
			<div class="control-group">
				Wpisz treść dedykacji
				<textarea class="span12" id="giftwrapmessage" rows="3">{{ giftwrap.message }}</textarea>
			</div>
			<button type="submit" class="btn btn-info" onclick="xajax_addGiftWrap($('#giftwrapmessage').val());return false;">Zatwierdź</button>
			<button type="submit" class="btn btn-inverse" onclick="xajax_deleteGiftWrap();return false;">Anuluj</button>
		</div>
	</td>
</tr>
{% endif %}