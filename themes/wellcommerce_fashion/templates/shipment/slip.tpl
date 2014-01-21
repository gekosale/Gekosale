<div>
	<table border="0">
		<tr>
			<td align="left">
				<table border="0">
					<tr>
						<th>
							<font size="12"><b>{% trans %}TXT_SHIPMENT{% endtrans %}</b> - {{ shipmentData.symbol }} - {{ shipmentData.shipmentdate }}</font><br />
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
<div id="asign">
	<table border="0">
		<tr>
			<td>Uwagi: {{ comment }}</td>
		</tr>
	</table>
</div>