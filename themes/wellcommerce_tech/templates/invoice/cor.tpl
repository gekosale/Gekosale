<div>
	<table border="0">	
		<tr>
			<td align="left"><font size="25">{{ companyaddress.shopname }}</font></td>
			<td align="right">
				<table style="border: 1px solid green;">
					<tr>
						<td align="center" colspan="2"><font size="12"><b>{{ invoiceData.symbol }}</b></font></td>
					</tr>
					<tr valign="middle" bordercolor="red">
						<td align="left"><font size="7" color="grey">{% trans %}TXT_INVOICE_DATE{% endtrans %}: {{ invoiceData.invoicedate }}</font></td>
						<td align="left"><font size="7" color="grey">{% trans %}TXT_SALE_DATE{% endtrans %}: {{ order.order_date }}</font></td>
					</tr>
					<tr valign="middle">
						<td align="left"><font size="7" color="grey">{% trans %}TXT_MATURITY{% endtrans %}: {{ invoiceData.duedate }}</font></td>
						<td align="left"><font size="7" color="grey">{% trans %}TXT_PAYMENT_METHOD{% endtrans %}:<br />{{ order.payment_method.paymentname }}</font></td>
					</tr>
					<tr>
						<td align="center" colspan="2"><font color="grey">{{ originalCopy }}</font></td>
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
						<th align="left">
							<font size="12"><b>Sprzedawca: </b></font><br />
							<font color="grey">
								{{ companyaddress.shopname }}<br />
								{{ companyaddress.street }} {{ companyaddress.streetno }} / {{ companyaddress.placeno }}<br />
								{{ companyaddress.postcode }} {{ companyaddress.placename }}<br />
								{% trans %}TXT_NIP{% endtrans %}: {{ companyaddress.nip }}
							</font>
						</th>
						<th>
							<font size="12"><b>Nabywca: </b></font><br />
							<font color="grey">
								{% if order.billing_address.companyname != '' %}
									{{ order.billing_address.companyname }}<br />
								{% else %}
									{{ order.billing_address.firstname }} {{ order.billing_address.surname }}<br />
								{% endif %}
								{% if order.billing_address.nip != '' %}
									{% trans %}TXT_NIP{% endtrans %}: {{ order.billing_address.nip }}
								{% endif %}
								{{ order.billing_address.street }} {{ order.billing_address.streetno }} / {{ order.billing_address.placeno }}<br />
								{{ order.billing_address.postcode }} {{ order.billing_address.city }}<br />
							</font>
						</th>
						<th>
							<font size="12"><b>Dostawa: </b></font><br />
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
								{{ order.delivery_method.deliverername }}
							</font>
						</th>
					</tr>
					<tr><th> </th></tr>
					<tr align="left">
						<th>
							<font color="grey">{{ companyaddress.bankname }}<br />{{ companyaddress.banknr }}</font>
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
						<td align="left" width="30%"><small><b>Nazwa towaru</b></small></td>
						<td align="center" width="5%"><small><b>Jedn.</b></small></td>
						<td align="center" width="5%"><small><b>Ilość</b></small></td>
						<td align="center" width="10%"><small><b>Stawka</b></small></td>
						<td align="center" width="10%"><small><b>Netto</b></small></td>
						<td align="center" width="15%"><small><b>Kwota VAT</b></small></td>
						<td align="center" width="20%"><small><b>Kwota brutto</b></small></td>
					</tr>
					{section name=i loop=$order.products}
					<tr>
						<td align="center" width="5%"><font color="grey"><small>{{ order.products[i].lp }}</small></font></td>
						<td align="left" width="30%"><font color="grey"><small>{{ order.products[i].name }}
						{if count($order.products[i].attributes) > 0}
							<br />{{ order.products[i].attributes.name }}
						{% endif %}
						</small></font></td>
						<td align="center" width="5%"><font color="grey"><small>szt.</small></font></td>
						<td align="center" width="5%"><font color="grey"><small>{{ order.products[i].quantity }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ order.products[i].vat }} %</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ order.products[i].net_subtotal }}</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ order.products[i].vat_value }}</small></font></td>
						<td align="center" width="20%"><font color="grey"><small>{{ order.products[i].subtotal }}</small></font></td>
					</tr>	
					{% endfor %}	
					<tr><td colspan="8"><br /></td></tr>
					<tr>
						<td align="right" width="45%" colspan="4"><font color="grey">razem</font></td>
						<td align="center" width="10%"><font color="grey"><small>X</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ total.netto }} {{ order.currencysymbol }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ total.vatvalue }} {{ order.currencysymbol }}</small></font></td>
						<td align="center" width="20%"><font color="grey"><small>{{ total.brutto }} {{ order.currencysymbol }}</small></font></td>
					</tr>
					{section name=s loop=$summary}
					<tr>
						<td align="right" width="45%" colspan="4"><font color="grey">w tym</font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ summary[s].vat }} %</small></font></td>
						<td align="center" width="15%"><font color="grey"><small>{{ summary[s].netto }} {{ order.currencysymbol }}</small></font></td>
						<td align="center" width="10%"><font color="grey"><small>{{ summary[s].vatvalue }} {{ order.currencysymbol }}</small></font></td>
						<td align="center" width="20%"><font color="grey"><small>{{ summary[s].brutto }} {{ order.currencysymbol }}</small></font></td>
					</tr>	
					{% endfor %} 
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="pricesumary" align="right">
	<table border="0" align="right">
		<tr>
			<td align="right"><font size="8" color="grey">{% trans %}TXT_IN_WORDS{% endtrans %}: {{ amountInWords }} </font></td>
		</tr>
	</table>
</div>
<br /><br />
<br /><br />
<br /><br />
<br /><br />
<div id="asign">
	<table border="0">
		<tr>
			<td align="center">................................................
				<br /><small> Imię i nazwisko osoby uprawnionej <br />do wystawienia faktur oraz pieczęć</small>
			</td>
			<td align="center">................................................
				<br /><small> Imię i nazwisko osoby uprawnionej <br />do odbierania faktur oraz pieczęć</small>
			</td>
		</tr>
	</table>
</div>