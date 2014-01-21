{% if coupon.code is defined %}
<tr>
	<td colspan="2">
		<h4>Kupon rabatowy: {{ coupon.code }}</h4>
		<button type="submit" class="btn btn-cancel" onclick="xajax_useCoupon();return false;">Anuluj</button>
	</td>
	<td colspan="2" class="alignright">Wysokość rabatu</td>
	<td colspan="2" class="center"><strong><span class="green">{{ couponvalue|priceFormat }}</span></strong></td>
</tr>
{% else %}
<tr>
	<td colspan="6">
		<h4>Kupon rabatowy</h4> <input type="text" id="coupon-code" class="input-large">
		<button type="submit" class="btn btn-info" onclick="xajax_useCoupon($('#coupon-code').val());return false;">Aktywuj</button>
	</td>
</tr>
{% endif %}