<tr>
	<td colspan="2"><h4>Transport i sposób płatności:</h4>{{ clientOrder.dispatchmethod.dispatchmethodname }} - {{ clientOrder.payment.paymentmethodname }}</td>
	<td colspan="2" class="alignright">{% trans %}TXT_COST_OF_DELIVERY{% endtrans %}</td>
	<td class="center"><strong>{{ clientOrder.dispatchmethod.dispatchmethodcost|priceFormat }}</strong></td>
</tr>