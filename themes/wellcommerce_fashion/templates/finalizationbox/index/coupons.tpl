{% if coupon.code is defined %}
<tr>
	<td colspan="4" class="alignright">Kupon rabatowy: {{ coupon.code }}</td>
	<td class="center"><strong><span class="green">{{ couponvalue|priceFormat }}</span></strong></td>
</tr>
{% endif %}