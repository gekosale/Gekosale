<tr>
	<td colspan="2" class="order-method">
		<h4>Wybierz kraj dostawy</h4>
		<div class="control-group ">
			<div class="controls nomargin">
				<select onchange="xajax_setDispatchmethodCountry(this.value);">
					{% for idcountry, name in countries %}
					<option value="{{ idcountry }}"  {% if idcountry == countrySelected %}selected{% endif %}>{{ name }}</option>
					{% endfor %}
				</select>
			</div>
		</div>
		{% if deliverymethods|length > 0 %}
		<h4>{% trans %}TXT_DELIVERY_TYPE{% endtrans %}</h4>
		<div class="control-group">
			{% for delivery in deliverymethods %}
			<label class="radio">
				<input type="radio" name="optionsRadios" id="delivery-{{ delivery.dispatchmethodid }}" value="{{ delivery.dispatchmethodid }}"  {% if delivery.dispatchmethodid == checkedDelivery.dispatchmethodid %}checked="checked"{% endif %} onclick="xajax_setDispatchmethodChecked({{ delivery.dispatchmethodid }}); return false;"> {{ delivery.name }} <span class="pull-right"><strong>{{ delivery.dispatchmethodcost|priceFormat }}</strong></span>
			</label>
			{% if delivery.options is not empty %}
                <label>
                	<select name="paczkomat" id="paczkomat" onchange="xajax_selectDeliveryOption({{ delivery.dispatchmethodid }}, this.value); return false;"">
                		<option value="0">-- wybierz paczkomat z listy --</option>
                		{% for city, option in delivery.options %}
                		<optgroup label="{{ city }}">
                		{% for paczkomat in option %}
                			<option value="{{ paczkomat.id }}" {% if checkedDeliveryOption.option == paczkomat.id %}selected{% endif %} >{{ paczkomat.label }}</option>
                		{% endfor %}
                		</optgroup>
                		{% endfor %}
                	</select>
                </label>
                {% endif %}
			{% endfor %}
		</div>
		<h4>{% trans %}TXT_PAYMENT_TYPE{% endtrans %}</h4>
		<div class="control-group">
			{% for payment in payments %}
			<label class="radio"><input type="radio" name="paymentsradio" id="payment-{{ payment.idpaymentmethod }}" value="{{ payment.name }}" {% if payment.idpaymentmethod == checkedPayment.idpaymentmethod %}checked="checked"{% endif %} onclick="xajax_setPeymentChecked({{ payment.idpaymentmethod }}, '{{ payment.name }}'); return false;"> {{ payment.name }}</label>
			{% endfor %}
		</div>
		{% endif %}
	</td>
	<td colspan="2" class="alignright">{% trans %}TXT_COST_OF_DELIVERY{% endtrans %}</td>
	<td colspan="2" class="center"><strong>{{ order.dispatchmethod.dispatchmethodcost|priceFormat }}</strong></td>
</tr>
						
						



