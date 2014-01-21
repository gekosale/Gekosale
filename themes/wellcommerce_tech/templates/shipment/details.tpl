<div>
	<table border="0">	
		<tr>
			<td align="left"><font size="25">{{ companyaddress.shopname }}</font></td>
			<td align="right">
				<table style="border: 1px solid green;">
					<tr>
						<td align="center"><font size="12">{% trans %}TXT_SHIPMENT{% endtrans %}<br /> <b>{{ shipmentData.symbol }}</b>.</font></td>
					</tr>
					<tr valign="middle" bordercolor="red">
						<td align="left"><font size="7" color="grey">{% trans %}TXT_SHIPMENT_DATE{% endtrans %}: {{ shipmentData.shipmentdate }}
                            <br/>
                            {% trans %}TXT_FOR_ORDER{% endtrans %}: {{ order.order_id }}
                            
                            </font></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div>
	<table border="0">
		<tr>
			<td align="left">
				<table border="0">
					<tr>
						<th>
							<font size="12"><b>{% trans %}TXT_SHIPMENT{% endtrans %}: </b></font><br />
							<font color="grey">
								{% if order.delivery_address.companyname != '' %}
									{{ order.delivery_address.companyname }}<br />
								{% else %}
									{{ order.delivery_address.firstname }} {{ order.delivery_address.surname }}<br />
								{% endif %}
								{% if order.delivery_address.nip != '' %}
									{% trans %}TXT_NIP{% endtrans %}: {{ order.delivery_address.nip }}
								{% endif %}
								{{ order.delivery_address.street }} {{ order.delivery_address.streetno }} / {{ order.delivery_address.placeno }}<br />
								{{ order.delivery_address.postcode }} {{ order.delivery_address.city }}<br />
                                {{ order.delivery_address.email }}<br />
                                {{ order.delivery_address.phone }}<br />
				{% if order.delivery_address.phone != '' %}{{ order.delivery_address.phone2 }}<br />{% endif %}
                                {{ order.shipmentData.dispatchernumber }}<br />
								<strong>{{ order.delivery_method.deliverername }}</strong>
							</font>
						</th>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="invoicedata">
    <font size="12"><b>{% trans %}TXT_ORDERED_PRODUCTS{% endtrans %}: </b></font><br/>
	<table border="0">
		<tr>
			<td align="left">
				<table border="1">
					<tr>
						<td align="center" width="5%"><small><b>Lp.</b></small></td>
						<td align="left" width="30%"><small><b>Nazwa towaru</b></small></td>
						<td align="center" width="5%"><small><b>Jedn.</b></small></td>
						<td align="center" width="5%"><small><b>Ilość</b></small></td>
						<td align="center" width="10%"><small><b>Stawka</b></small></td>
						<td align="center" width="10%"><small><b>Netto</b></small></td>
						<td align="center" width="15%"><small><b>Kwota VAT</b></small></td>
						<td align="center" width="20%"><small><b>Kwota brutto</b></small></td>
					</tr>
					{% for product in order.products %}
					<tr>
						<td align="center" width="5%"><font color="grey"><small>{{ product.lp }}</small></font></td>
						<td align="left" width="30%"><font color="grey"><small>{{ product.name }}
						{% if product.attributes|length > 0 %}
							<br />{{ product.attributes.name }}
						{% endif %}
						</small></font></td>
						<td align="center" width="5%"><font color="grey"><small>szt.</small></font></td>
						<td align="center" width="5%"><font color="grey"><small>{{ product.quantity }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ product.vat }} %</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ product.net_subtotal|priceFormat }}</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ product.vat_value|priceFormat }}</small></font></td>
						<td align="center" width="20%"><font color="grey"><small>{{ product.subtotal|priceFormat }}</small></font></td>
					</tr>	
					{% endfor %}	
					<tr><td colspan="8"><br /></td></tr>
					<tr>
						<td align="right" width="45%" colspan="4"><font color="grey">razem</font></td>
						<td align="center" width="10%"><font color="grey"><small>X</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ total.netto|priceFormat }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ total.vatvalue|priceFormat }}</small></font></td>
						<td align="center" width="20%"><font color="grey"><small>{{ total.brutto|priceFormat }}</small></font></td>
					</tr>
					{% for sum in summary %}
					<tr>
						<td align="right" width="45%" colspan="4"><font color="grey">w tym</font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ sum.vat }} %</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ sum.netto|priceFormat }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ sum.vatvalue|priceFormat }}</small></font></td>
						<td align="center" width="20%"><font color="grey"><small>{{ sum.brutto|priceFormat }}</small></font></td>
					</tr>	
					{% endfor %}
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="asign">
	<table border="0">
		<tr>
			<td>Uwagi: {{ comment }}</td>
		</tr>
	</table>
</div>