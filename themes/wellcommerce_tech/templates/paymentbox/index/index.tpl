<fieldset class="payment">

{% for payment in payments %}
	<div class="field-radio">
		<label>
		{% if payment.idpaymentmethod == checkedPayment.idpaymentmethod %}
			<input type="radio" name="paymentsradio" id="{{ payment.idpaymentmethod }}" value="{{ payment.name }}" checked="checked" onclick="xajax_setPeymentChecked(id, value); return false;" />
		{% else %}
			<input type="radio" name="paymentsradio" id="{{ payment.idpaymentmethod }}" value="{{ payment.name }}" onclick="xajax_setPeymentChecked(id, value); return false;" /> 
		{% endif %}
		{% if  payment.wariantsklepu is defined and payment.wariantsklepu > 0 and payment.numersklepu is defined and payment.numersklepu > 0 %}
		<span>
			{{ payment.name }}
			<a href="https://www.eraty.pl/symulator/oblicz.php?numerSklepu={{ payment.numersklepu }}&wariantSklepu={{ payment.wariantsklepu }}&typProduktu=0&wartoscTowarow={{ priceWithDispatch }}" target='_blank' > Policz ratę </a>
		</span>
		{% else %}
		<span>{{ payment.name }} </span>
		{% endif %}
		</label>
	</div>
{% endfor %}

</fieldset>
						
<div class="buttons" id="action_buttons">
	<a id="payment-prev" href="{{ path('frontend.delivery') }}/"><img src="{{ DESIGNPATH }}/_images_frontend/buttons/wstecz.png" alt="{% trans %}TXT_BACK{% endtrans %}"/></a>
	{% if checkedPayment is defined and checkedPayment != 0 %}
	<a id="payment-next" href="{{ path('frontend.finalization') }}/"><img src="{{ DESIGNPATH }}/_images_frontend/buttons/dalej.png" alt="{% trans %}TXT_NEXT{% endtrans %}" value="{% trans %}TXT_NEXT{% endtrans %}"/></a>
	{% endif %}
</div>			

<script type="text/javascript">

$(document).ready(function() {

	$('.tabs').parent().tabs({
		disabled: [3]
	});
	$('input[name=paymentsradio]').click(function(){
		$('.tabs').parent().tabs({
			disabled: []
		});
	});
	if($('input[name=paymentsradio]:checked').val()){
		$('.tabs').parent().tabs({
			disabled: []
		});
	}

	$('#payment-prev').live('click',function(){
		$('.tabs').parent().tabs("select",1);
		return false;
	});
	
	$('#payment-next').live('click',function(){
		$('.tabs').parent().tabs("select",3);
		return false;
	});
});

</script>
