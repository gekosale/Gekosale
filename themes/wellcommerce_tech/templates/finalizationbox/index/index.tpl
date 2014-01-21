{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %} {% block content %}
<h1 class="large">Krok 2. Podsumowanie zamówienia</h1>
<div class="row-fluid row-form">
	<form class="form-horizontal" id="order">
		<table class="table table-striped table-bordered products-table">
			<thead>
				<tr>
					<th style="width: 88px;"></th>
					<th>{% trans %}TXT_PRODUCT_NAME{% endtrans %}</th>
					<th style="width: 80px">{% trans %}TXT_PRICE{% endtrans %}</th>
					<th style="width: 85px">{% trans %}TXT_PRODUCT_QUANTITY{% endtrans %}</th>
					<th style="width: 100px">{% trans %}TXT_PRODUCT_SUBTOTAL{% endtrans %}</th>
				</tr>
			</thead>
			<tbody>
				{% include 'finalizationbox/index/products.tpl' %}
			</tbody>
			<tfoot>
				{% include 'finalizationbox/index/coupons.tpl' %}
				{% include 'finalizationbox/index/methods.tpl' %}
				{% include 'finalizationbox/index/client.tpl' %}
				{% include 'finalizationbox/index/comment.tpl' %}
				{% include 'finalizationbox/index/discounts.tpl' %}
				{% include 'finalizationbox/index/summary.tpl' %}
			</tfoot>
		</table>
		<div class="pull-right">
			<a href="{{ path('frontend.cart') }}" title=""><i class="icon icon-arrow-left-blue"></i> Wróć	do koszyka</a>
			<button type="submit" id="save-order" class="btn btn-large btn-primary marginlt20">Wyślij zamówienie</button>
		</div>
	</form>
</div>

<div id="changeContactData" class="modal fade hide">
	<div class="modal-header">
		<h3>{% trans %}TXT_CONTACT_DATA{% endtrans %}</h3>
	</div>
	<div class="modal-body">
		<div class="row-fluid row-form">
			<div class="span9">
				<form name="{{ formContact.name }}" id="{{ formContact.name }}" method="{{ formContact.method }}" action="{{ formContact.action }}">
					<input type="hidden" name="{{ formContact.submit_name }}" value="1" />
					<fieldset>
						<div class="row-fluid">
		                	<div class="span6">
								{{ forms.input(formContact.children.phone, 'span12') }}
							</div>	
		                	<div class="span6">
								{{ forms.input(formContact.children.phone2, 'span12') }}
							</div>	
						</div>
						<div class="row-fluid">
							<div class="span6">
								{{ forms.input(formContact.children.email, 'span12') }}
							</div>
						</div>
					</fieldset>
					{{ forms.hidden(formContact.children.__csrf) }}
					{{ formContact.javascript }}
				</form>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">{% trans %}TXT_CLOSE{% endtrans %}</a> 
		<button type="submit" class="btn btn-primary" onclick="$('#{{ formContact.name }}').submit();">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
	</div>
</div>

<div id="changeBillingData" class="modal fade hide">
	<div class="modal-header">
		<h3>{% trans %}TXT_INVOICE_DATA{% endtrans %}</h3>
	</div>
	<div class="modal-body">
		<div class="row-fluid row-form">
			<div class="span9">
				<form name="{{ formBilling.name }}" id="{{ formBilling.name }}" method="{{ formBilling.method }}" action="{{ formBilling.action }}">
					<input type="hidden" name="{{ formBilling.submit_name }}" value="1" />
					<fieldset>
						
						{{ forms.radio(formBilling.children.clienttype) }}
						
						<div class="row-fluid">
		                	<div class="span6">
								{{ forms.input(formBilling.children.firstname, 'span12') }}
							</div>	
							<div class="span6">
								{{ forms.input(formBilling.children.surname, 'span12') }}
							</div>
						</div>
						<div id="billing-company-data" class="collapse {% if formBilling.children.clienttype.value == 2 %}in{% endif %}">
							<div class="span6">
								{{ forms.input(formBilling.children.companyname, 'span12') }}
							</div>
							<div class="span6">
								{{ forms.input(formBilling.children.nip, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
		                	<div class="span6">
								{{ forms.input(formBilling.children.street, 'span12') }}
							</div>	
							<div class="span3">
								{{ forms.input(formBilling.children.streetno, 'span12') }}
							</div>
							<div class="span3">
								{{ forms.input(formBilling.children.placeno, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
		                	<div class="span3">
								{{ forms.input(formBilling.children.postcode, 'span12') }}
							</div>	
							<div class="span9">
								{{ forms.input(formBilling.children.placename, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
		                	<div class="span12">
		                		{{ forms.select(formBilling.children.countryid, 'span6') }}
		                	</div>
		                </div>
					</fieldset>
					{{ forms.hidden(formBilling.children.__csrf) }}
					{{ formBilling.javascript }}
				</form>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">{% trans %}TXT_CLOSE{% endtrans %}</a> 
		<button type="submit" class="btn btn-primary" onclick="$('#{{ formBilling.name }}').submit();">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
	</div>
</div>
<div id="changeDeliveryData" class="modal fade hide">
	<div class="modal-header">
		<h3>{% trans %}TXT_DELIVERER_ADDRESS{% endtrans %}</h3>
	</div>
	<div class="modal-body">
		<div class="row-fluid row-form">
			<div class="span9">
				<form name="{{ formShipping.name }}" id="{{ formShipping.name }}" method="{{ formShipping.method }}" action="{{ formShipping.action }}">
					<input type="hidden" name="{{ formShipping.submit_name }}" value="1" />
					<fieldset>
						<div class="row-fluid">
		                	<div class="span6">
								{{ forms.input(formShipping.children.firstname, 'span12') }}
							</div>	
							<div class="span6">
								{{ forms.input(formShipping.children.surname, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								{{ forms.input(formShipping.children.companyname, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
		                	<div class="span6">
								{{ forms.input(formShipping.children.street, 'span12') }}
							</div>	
							<div class="span3">
								{{ forms.input(formShipping.children.streetno, 'span12') }}
							</div>
							<div class="span3">
								{{ forms.input(formShipping.children.placeno, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
		                	<div class="span3">
								{{ forms.input(formShipping.children.postcode, 'span12') }}
							</div>	
							<div class="span9">
								{{ forms.input(formShipping.children.placename, 'span12') }}
							</div>
						</div>
						<div class="row-fluid">
		                	<div class="span12">
		                		{{ forms.select(formShipping.children.countryid, 'span6') }}
		                	</div>
		                </div>
					</fieldset>
				{{ forms.hidden(formShipping.children.__csrf) }}
					{{ formShipping.javascript }}
				</form>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">{% trans %}TXT_CLOSE{% endtrans %}</a> 
		<button type="submit" class="btn btn-primary" onclick="$('#{{ formShipping.name }}').submit();">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
	</div>
</div>

<script type="text/javascript">
$("#{{ formBilling.name }} input[name='clienttype']").unbind('change').bind('change', function(){
	$('#billing-company-data').collapse($(this).val() == 2 ? 'show' : 'hide');
});
$('#order').unbind('submit').bind('submit', function(e){
	e.preventDefault();
	$('#save-order').hide();
	xajax_saveOrder({
		customeropinion: $('#customeropinion').val()
	});
});
</script>
{% endblock %}
