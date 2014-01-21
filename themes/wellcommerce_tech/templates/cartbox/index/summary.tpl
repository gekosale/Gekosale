<tr class="special">
	<td colspan="4" class="alignright"><h4>Łącznie do zapłaty</h4>Metoda płatności: <strong>{{ checkedPayment.paymentmethodname }}</strong></td>
	{% if order.priceWithDispatchMethodPromo is not defined %}
	<td colspan="2" class="center"><h3>{{ order.priceWithDispatchMethod|priceFormat }}</h3></td>
	{% else %}
	<td colspan="2" class="center"><h3>{{ order.priceWithDispatchMethodPromo|priceFormat }}</h3></td>
	{% endif %}
</tr>