<div>
	<table border="0">	
		<tr>
			<td align="left"><font size="25">{% trans %}TXT_ORDER{% endtrans %}: {{ order.order_id }}<br /><small>{% trans %}TXT_SALE_DATE{% endtrans %}: {{ order.order_date }}</small></font></td>
		</tr>
	</table>
</div>
<div>
	<table border="0">
		<tr>
			<td align="left">
				<table border="0">
					<tr>
						<th align="left">
							<font size="12"><b>{% trans %}TXT_SELLER{% endtrans %}: </b></font><br />
							<font color="grey">
								{{ companyaddress.shopname }}<br />
								{{ companyaddress.street }} {{ companyaddress.streetno }}{% if companyaddress.placeno != '' %} / {{ companyaddress.placeno }}{% endif %}<br />
								{{ companyaddress.postcode }} {{ companyaddress.placename }}<br />
								{% trans %}TXT_NIP{% endtrans %}: {{ companyaddress.nip }}
							</font>
						</th>
						<th>
							<font size="12"><b>{% trans %}TXT_TRANSFEREE{% endtrans %}: </b></font><br />
							<font color="grey">
								{% if order.billing_address.companyname != '' %}
									{{ order.billing_address.companyname }}<br />
								{% else %}
									{{ order.billing_address.firstname }} {{ order.billing_address.surname }}<br />
								{% endif %}
								{% if order.billing_address.nip != '' %}
									{% trans %}TXT_NIP{% endtrans %}: {{ order.billing_address.nip }}
								{% endif %}
								{{ order.billing_address.street }} {{ order.billing_address.streetno }}{% if order.billing_address.placeno != '' %} / {{ order.billing_address.placeno }}{% endif %}<br />
								{{ order.billing_address.postcode }} {{ order.billing_address.city }}<br />
								{% trans %}TXT_PHONE{% endtrans %}: {{ order.billing_address.phone }}<br />
								{{ order.billing_address.email }}<br />
							</font>
						</th>
						<th>
							<font size="12"><b>{% trans %}TXT_DELIVERER_ADDRESS{% endtrans %}: </b></font><br />
							<font color="grey">
								{% if order.delivery_address.companyname != '' %}
									{{ order.delivery_address.companyname }}<br />
								{% else %}
									{{ order.delivery_address.firstname }} {{ order.delivery_address.surname }}<br />
								{% endif %}
								{% if order.delivery_address.nip != '' %}
									{% trans %}TXT_NIP{% endtrans %}: {{ order.delivery_address.nip }}
								{% endif %}
								{{ order.delivery_address.street }} {{ order.delivery_address.streetno }} {% if order.delivery_address.placeno != '' %} / {{ order.delivery_address.placeno }}{% endif %}<br />
								{{ order.delivery_address.postcode }} {{ order.delivery_address.city }}<br />
								{% trans %}TXT_PHONE{% endtrans %}: {{ order.delivery_address.phone }}<br />
								{{ order.delivery_address.email }}<br />
								{{ order.delivery_method.deliverername }}
							</font>
						</th>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="invoicedata">
	<table border="0">
		<tr>
			<td align="left">
				<table border="1">
					<tr>
						<td align="center" width="5%"><small><b>Lp.</b></small></td>
						<td align="left" width="{{ isCoupon == true ? 25 : 30 }}%"><small><b>{% trans %}TXT_PRODUCT_NAME{% endtrans %}</b></small></td>
						<td align="center" width="10%"><small><b>{% trans %}TXT_UNIT_MEASURE{% endtrans %}</b></small></td>
						<td align="center" width="{{ isCoupon == true ? 5 : 10 }}%"><small><b>{% trans %}TXT_PRODUCT_QUANTITY{% endtrans %}</b></small></td>
						<td align="center" width="10%"><small><b>{% trans %}TXT_JS_RANGE_EDITOR_VAT{% endtrans %}</b></small></td>
						<td align="center" width="10%"><small><b>{% trans %}TXT_JS_PRODUCT_SELECT_NET_SUBSUM{% endtrans %}</b></small></td>
						{% if isCoupon == true %}
							<td align="center" width="10%"><small><b>Netto po rabacie</b></small></td>
						{% endif %}
						<td align="center" width="10%"><small><b>{% trans %}TXT_VAT_AMOUNT{% endtrans %}</b></small></td>
						<td align="center" width="15%"><small><b>{% trans %}TXT_JS_PRODUCT_SELECT_SUBSUM{% endtrans %}</b></small></td>
					</tr>
					{% for product in order.products %}
					<tr>
						<td align="center" width="5%"><font color="grey"><small>{{ product.lp }}</small></font></td>
						<td align="left" width="{{ isCoupon == true ? 25 : 30 }}%"><font color="grey"><small>{{ product.name }}
						{% for attribute in product.attributes %}
							<br />{{ attribute.name }}
						{% endfor %}
						</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>szt.</small></font></td>
						<td align="center" width="{{ isCoupon == true ? 5 : 10 }}%"><font color="grey"><small>{{ product.quantity }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ product.vat }} %</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ product.net_subtotal|priceFormat }}</small></font></td>
						{% if isCoupon == true %}
							<td align="center" width="10%"><font color="grey"><small>{{ product.discount_net_subtotal|priceFormat }}</small></font></td>
						{% endif %}
						<td align="center" width="10%"><font color="grey"><small>{{ product.vat_value|priceFormat }}</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ product.subtotal|priceFormat }}</small></font></td>
					</tr>	
					{% endfor %}	
					<tr><td colspan="{{ isCoupon == true ? 9 : 8 }}"><br /></td></tr>
					<tr>
						<td align="right" width="45%" colspan="4"><font color="grey">{% trans %}TXT_TOGETHER{% endtrans %}</font></td>
						<td align="center" width="10%"><font color="grey"><small>X</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ total.netto|priceFormat }}</small></font></td>
						{% if isCoupon == true %}
							<td align="center" width="10%"><font color="grey"><small>{{ total.discount_netto|priceFormat }}</small></font></td>
						{% endif %}
						<td align="center" width="10%"><font color="grey"><small>{{ total.vatvalue|priceFormat }}</small></font></td>
						<td align="center" width="{{ isCoupon == true ? 15 : 25 }}%"><font color="grey"><small>{{ total.brutto|priceFormat }}</small></font></td>
					</tr>
					{% for sum in summary %}
					<tr>
						<td align="right" width="45%" colspan="4"><font color="grey">{% trans %}TXT_CONTAIN{% endtrans %}</font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ sum.vat }} %</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ sum.netto|priceFormat }}</small></font></td>
						{% if isCoupon == true %}
							<td align="center" width="10%"><font color="grey"><small>{{ sum.discount_netto|priceFormat }}</small></font></td>
						{% endif %}
						<td align="center" width="10%"><font color="grey"><small>{{ sum.vatvalue|priceFormat }}</small></font></td>
						<td align="center" width="{{ isCoupon == true ? 15 : 25 }}%"><font color="grey"><small>{{ sum.brutto|priceFormat }}</small></font></td>
					</tr>	
					{% endfor %}
				</table>
			</td>
		</tr>
	</table>
</div>
			
<div id="pricesumary" align="right">
	<table border="0">	
		<tr >
			<td align="left"><font size="8" color="grey">{% trans %}TXT_PAYMENT_METHOD{% endtrans %}:<br />{{ order.payment_method.paymentname }}</font></td>
		</tr>
	</table>
</div>
<div id="pricesumary" align="right">
	<table border="0" align="right">
		<tr>
			<td align="left"><font size="8" color="grey">{% trans %}TXT_COMMENT{% endtrans %}: {{ order.customeropinion }} </font></td>
		</tr>
	</table>
</div>
