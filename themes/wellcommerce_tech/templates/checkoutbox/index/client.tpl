{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
<h1 class="large">Krok 1. Dane adresowe</h1>
<div class="row-fluid row-form">
	<div class="span9">
		<form  name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}">
			<input type="hidden" name="{{ form.submit_name }}" value="1" />
			<fieldset>
				<div class="well well-small well-clean">
					<legend>
						Dane zamawiającego <small>*{% trans %}TXT_REQUIRED_FIELD{% endtrans %}</small>
					</legend>
					{{ forms.radio(form.children.billing_clienttype) }}
					<div class="row-fluid">
						<div class="span6">
							{{ forms.input(form.children.billing_firstname, 'span12') }}
						</div>
						<div class="span6">
							{{ forms.input(form.children.billing_surname, 'span12') }}
						</div>
					</div>
					<div class="row-fluid collapse {% if form.children.billing_clienttype.value == 2 %}in{% endif %}" id="billing-company-data">
						<div class="span6">
							{{ forms.input(form.children.billing_companyname, 'span12') }}
						</div>
						<div class="span6">
							{{ forms.input(form.children.billing_nip, 'span12') }}
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							{{ forms.input(form.children.billing_street, 'span12') }}
						</div>
						<div class="span3">
							{{ forms.input(form.children.billing_streetno, 'span12') }}
						</div>
						<div class="span3">
							{{ forms.input(form.children.billing_placeno, 'span12') }}
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							{{ forms.input(form.children.billing_placename, 'span12') }}
						</div>
						<div class="span3">
							{{ forms.input(form.children.billing_postcode, 'span12') }}
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							{{ forms.select(form.children.billing_country, 'span12') }}
						</div>
					</div>
					<div class="row-fluid collapse {% if formBilling.children.clienttype.value == 2 %}in{% endif %}" id="billing-company-invoice"><div class="control-group">
						<div class="controls">
							<span class="help-block gray"><small>Na powyższe dane zostanie wystawona faktura VAT</small></span>
						</div>
					</div>

					{{ forms.checkbox(form.children.other_address, 'span12') }}

					<div id="shipping-data" class="collapse">
						<div class="row-fluid">
							<div class="span6">
								{{ forms.input(form.children.shipping_firstname, 'span12') }}
							</div>
							<div class="span6">
								{{ forms.input(form.children.shipping_surname, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								{{ forms.input(form.children.shipping_companyname, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								{{ forms.input(form.children.shipping_street, 'span12') }}
							</div>
							<div class="span3">
								{{ forms.input(form.children.shipping_streetno, 'span12') }}
							</div>
							<div class="span3">
								{{ forms.input(form.children.shipping_placeno, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								{{ forms.input(form.children.shipping_placename, 'span12') }}
							</div>
							<div class="span3">
								{{ forms.input(form.children.shipping_postcode, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								{{ forms.select(form.children.shipping_country, 'span12') }}
							</div>
						</div>
					</div>

					<legend class="marginbt10">Dane kontaktowe</legend>

					<div class="row-fluid">
						<div class="span6">
							{{ forms.input(form.children.phone, 'span12') }}
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							{{ forms.input(form.children.phone2, 'span12') }}
						</div>
					</div>
					<div class="row-fluid marginbt20">
						<div class="span6">
							{{ forms.input(form.children.email, 'span12') }}
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</fieldset>
			<fieldset>
					{{ forms.hidden(form.children.__csrf) }}
				<div class="form-actions form-actions-clean pull-right">
					<a href="{{ path('frontend.cart') }}" title=""><i class="icon icon-arrow-left-blue"></i> {% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</a>
					<button type="submit" class="btn btn-large btn-primary marginlt20">{% trans %}TXT_CONFIRM_ORDER_DATA{% endtrans %} <i class="icon icon-arrow-right icon-white"></i></button>
				</div>
			</fieldset>
			{{ form.javascript }}
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#billing-company-data').find('input').attr('tabindex', -1);
	$("#{{ form.name }} input[name='billing_clienttype']").unbind('change').bind('change', function(){
		$('#billing-company-data').collapse($(this).val() == 2 ? 'show' : 'hide');
		$('#billing-company-invoice').collapse($(this).val() == 2 ? 'show' : 'hide');
		if($(this).val() == 2){
			$('#billing-company-data').find('input').removeAttr('tabindex');
		}else{
			$('#billing-company-data').find('input').attr('tabindex', -1);
		}
	});
	$("#{{ form.name }} input[name='other_address']").unbind('change').bind('change', function(){
		$('#shipping-data').collapse($(this).is(':checked') ? 'show' : 'hide');
	});
});
</script>
{% endblock %}
