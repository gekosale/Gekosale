{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
<h1 class="large">Krok 1. Dane adresowe</h1>
<div class="row-fluid row-form">
	<div class="span9">
		<div class="span3 alignright">
			<h3 class="normal font20">Posiadam konto</h3>
		</div>
		<div class="span6">
			{% if loginerror is defined %}
			<div class="alert alert-error">
				<strong>{{ loginerror }}</strong>
			</div>
			{% endif %}
			<form name="{{ formLogin.name }}" id="{{ formLogin.name }}"	method="{{ formLogin.method }}" action="{{ formLogin.action }}">
				<input type="hidden" name="{{ formLogin.submit_name }}" value="1" />
				<fieldset>
					<div class="well well-small">
						<div class="login-form">
							<legend>
								{% trans %}TXT_LOGIN{% endtrans %} <small>*{% trans %}TXT_REQUIRED_FIELD{% endtrans %}</small>
							</legend>
							{{ forms.input(formLogin.children.login, 'input-xlarge') }}
							{{ forms.password(formLogin.children.password, 'input-xlarge') }}
							{{ forms.hidden(formLogin.children.__csrf) }}
							<div class="form-actions form-actions-clean">
								<a href="{{ path('frontend.forgotpassword') }}" title="{% trans %}TXT_FORGOT_PASSWORD{% endtrans %}">{% trans %}TXT_FORGOT_PASSWORD{% endtrans %}</a>
								<button type="submit" class="btn btn-large btn-primary pull-right">{% trans %}TXT_LOGIN{% endtrans %}</button>
							</div>
						</div>
						<div class="login-info">
							<h4>Jesteś stałym klientem</h4>
							<ul>
								<li>Kupujesz szybko i wygodnie</li>
								<li>Możesz śledzić bieżące zamówienia</li>
								<li>Otrzymujesz informacje o promocjach</li>
							</ul>
						</div>
						<div class="clearfix"></div>
					</div>
				</fieldset>
				{{ formLogin.javascript }}
			</form>
		</div>
		<div class="clearfix"></div>
		<div class="span3 alignright">
			<h3 class="normal font20">Kupuję pierwszy raz</h3>
			<h4 class="normal font15">
				Kupuj jako gość<br>lub zarejestruj się
			</h4>
		</div>
		<div class="span6">
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
						<div class="row-fluid collapse {% if formBilling.children.clienttype.value == 2 %}in{% endif %}" id="billing-company-data">
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
					</div>
					{% if enableregistration != 0 %}
					<div class="well well-small">
						{{ forms.checkbox(form.children.create_account, 'span12') }}
						<div class="collapse in" id="create-account">
							<legend class="marginbt10">Dane konta</legend>
							<div>
								<div class="password-form">
									{{ forms.password(form.children.password, 'span12') }}
									{{ forms.password(form.children.confirmpassword, 'span12') }}
								</div>
								<div class="password-info">
									<h4>Korzyści z założenia konta</h4>
									<ul>
										<li>Aktualny <strong>status przesyłki</strong></li>
										<li><strong>Rabaty</strong> dla stałych klientów</li>
									</ul>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
					{% endif %}
				</fieldset>
				<fieldset>
					<div class="well well-small">
						<legend>Warunki sklepu oraz biuletyn okazji</legend>
						{{ forms.checkbox(form.children.confirmterms) }}
						{{ forms.checkbox(form.children.newsletter) }}
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

	$("#{{ form.name }} input[name='create_account']").unbind('change').bind('change', function(){
		$('#create-account').collapse($(this).is(':checked') ? 'show' : 'hide');
	});

	{% if enableregistration != 0 %}
	$('#order_create_account').click(function() {
		if(!$(this).is(':checked')) {
			$('#order_confirmpassword').val('');
			$('#order_password').val('');
		}
	});
	{% endif %}
});
</script>
{% endblock %}
