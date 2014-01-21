{% autoescape true %}
<tr>
	<td colspan="5" class="order-address">
		<div class="row-fluid">
			<div class="span4">
				<h4>
					{% trans %}TXT_DELIVERER_ADDRESS{% endtrans %} <a href="#changeDeliveryData" data-toggle="modal">{% trans %}TXT_CHANGE{% endtrans %}</a>
				</h4>
				<p>
					{% if clientOrder.deliveryAddress.companyname != '' %}{{clientOrder.deliveryAddress.companyname}}<br />{% endif %}
					{{ clientOrder.deliveryAddress.firstname }} {{ clientOrder.deliveryAddress.surname }}<br />
					{{ clientOrder.deliveryAddress.street }} {{ clientOrder.deliveryAddress.streetno }}{% if clientOrder.deliveryAddress.placeno != '' %}/{{clientOrder.deliveryAddress.placeno}}{% endif %}<br />
					{{ clientOrder.deliveryAddress.postcode }} {{ clientOrder.deliveryAddress.placename }}
				</p>
			</div>
			<div class="span4">
				<h4>
					{% trans %}TXT_CONTACT_DATA{% endtrans %} <a href="#changeContactData" data-toggle="modal">{% trans %}TXT_CHANGE{% endtrans %}</a>
				</h4>
				<p>
					{{ clientOrder.contactData.email }}<br>{{ clientOrder.contactData.phone }} {% if clientOrder.contactData.phone2 != '' %}<br />{{ clientOrder.contactData.phone2 }}{% endif %}
				</p>
			</div>
			<div class="span4">
				<h4>
					{% trans %}TXT_INVOICE_DATA{% endtrans %} <a href="#changeBillingData" data-toggle="modal">{% trans %}TXT_CHANGE{% endtrans %}</a>
				</h4>
				<p>
					{% if clientOrder.clientaddress.companyname != '' %}{{clientOrder.clientaddress.companyname}}<br />{% endif %}
					{% if clientOrder.clientaddress.nip != '' %}{{clientOrder.clientaddress.nip}}<br />{% endif %}
					{{ clientOrder.clientaddress.firstname }} {{ clientOrder.clientaddress.surname }}<br />
					{{ clientOrder.clientaddress.street }} {{ clientOrder.clientaddress.streetno }}{% if clientOrder.clientaddress.placeno != '' %}/{{clientOrder.deliveryAddress.placeno}}{% endif %}<br />
					{{ clientOrder.clientaddress.postcode }} {{ clientOrder.clientaddress.placename }}
				</p>
			</div>
		</div>
	</td>
</tr>
{% endautoescape %}