{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
{% if mailsend is defined %}
	<p class="error"><strong>{% trans %}TXT_ATTENTION{% endtrans %}: {% trans %}TXT_EMAIL_SENT{% endtrans %}</strong></p>
{% endif %}

<article class="article">
	<h1>{% trans %}TXT_CLIENT_ADDRESS{% endtrans %}</h1>
	<form class="form-horizontal well" name="{{ formBilling.name }}" id="{{ formBilling.name }}" method="{{ formBilling.method }}" action="{{ formBilling.action }}">
		<input type="hidden" name="{{ formBilling.submit_name }}" value="1" />
		<fieldset>
			<legend>
				Dane zamawiającego <small>*{% trans %}TXT_REQUIRED_FIELD{% endtrans %}</small>
			</legend>

			{{ forms.radio(formBilling.children.clienttype) }}

			{{ forms.input(formBilling.children.firstname, 'input-xlarge') }}

			{{ forms.input(formBilling.children.surname, 'input-xlarge') }}

			<div id="billing-company-data" class="collapse {% if formBilling.children.clienttype.value == 2 %}in{% endif %}">
				{{ forms.input(formBilling.children.companyname, 'input-xlarge') }}
				{{ forms.input(formBilling.children.nip, 'input-xlarge') }}
			</div>

			{{ forms.input(formBilling.children.street, 'input-xlarge') }}

			{{ forms.input(formBilling.children.streetno, 'input-xlarge') }}

			{{ forms.input(formBilling.children.placeno, 'input-xlarge') }}

			{{ forms.input(formBilling.children.postcode, 'input-xlarge') }}

			{{ forms.input(formBilling.children.placename, 'input-xlarge') }}

			{{ forms.select(formBilling.children.countryid, 'input-xlarge') }}

			{{ forms.hidden(formBilling.children.__csrf) }}
			<div class="form-actions form-actions-clean">
				<button type="submit" class="btn btn-large btn-primary">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
			</div>
		</fieldset>
		{{ formBilling.javascript }}
	</form>
{% autoescape true %}
	<form class="form-horizontal padding19 nomarginbt">
		<legend>{% trans %}TXT_DELIVER_DATA{% endtrans %} <a href="#changeDeliveryData" data-toggle="modal" title="">{% trans %}TXT_CHANGE{% endtrans %}</a></legend>
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_FIRSTNAME{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.firstname }}</strong>
			</div>
		</div>
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_SURNAME{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.surname }}</strong>
			</div>
		</div>
		{% if clientShippingAddress.clienttype == 2 %}
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_COMPANYNAME{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.companyname }}</strong>
			</div>
		</div>
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_NIP{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.nip }}</strong>
			</div>
		</div>
		{% endif %}
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_PLACENAME{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.placename }}</strong>
			</div>
		</div>
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_POSTCODE{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.postcode }}</strong>
			</div>
		</div>
		<div class="control-group group-text">
			<label class="control-label" for="">{% trans %}TXT_ADDRESS{% endtrans %}:</label>
			<div class="controls">
				<strong>{{ clientShippingAddress.street }} {{ clientShippingAddress.streetno }}{% if clientShippingAddress.placeno != '' %}/{{ clientShippingAddress.placeno }}{% endif %}</strong>
			</div>
		</div>
	</form>
{% endautoescape %}
</article>
<div class="head-block">
	<span class="font">Oferta dla Ciebie</span>
</div>
<div id="recommendations">
	{{ recommendations(4) }}
</div>
<div id="changeDeliveryData" class="modal fade hide">
	<div class="modal-header">
		<h3>{% trans %}TXT_DELIVER_DATA{% endtrans %}</h3>
	</div>
	<div class="modal-body">
	<div class="row-fluid row-form">

                <div class="span9">

		<form name="{{ formShipping.name }}" id="{{ formShipping.name }}" method="{{ formShipping.method }}" action="{{ formShipping.action }}">
			<input type="hidden" name="{{ formShipping.submit_name }}" value="1" />
			<fieldset>
				{{ forms.radio(formShipping.children.clienttype) }}
				<div class="row-fluid">
                	<div class="span6">
						{{ forms.input(formShipping.children.firstname, 'span12') }}
					</div>
					<div class="span6">
						{{ forms.input(formShipping.children.surname, 'span12') }}
					</div>
				</div>
				<div id="shipping-company-data" class="collapse {% if formShipping.children.clienttype.value == 2 %}in{% endif %}">
					{{ forms.input(formShipping.children.companyname, 'span12') }}
					{{ forms.input(formShipping.children.nip, 'span12') }}
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
                		{{ forms.select(formShipping.children.countryid, 'span12') }}
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
		<a href="#" class="btn" data-dismiss="modal">{% trans %}Close{% endtrans %}</a>
		<button type="submit" class="btn btn-primary" onclick="$('#{{ formShipping.name }}').submit();">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#billing-company-data').find('input').attr('tabindex', -1);
	$("#{{ formBilling.name }} input[name='clienttype']").unbind('change').bind('change', function(){
		$('#billing-company-data').collapse($(this).val() == 2 ? 'show' : 'hide');
		if($(this).val() == 2){
			$('#billing-company-data').find('input').removeAttr('tabindex');
		}else{
			$('#billing-company-data').find('input').attr('tabindex', -1);
		}
	});
	$("#{{ formShipping.name }} input[name='clienttype']").unbind('change').bind('change', function(){
		$('#shipping-company-data').collapse($(this).val() == 2 ? 'show' : 'hide');
	});
});
</script>
{% endblock %}
