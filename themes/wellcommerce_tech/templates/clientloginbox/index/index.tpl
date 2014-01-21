{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
{% if clientChangedMail is defined %}
<div class="alert alert-success">
	<strong>{{ clientChangedMail }}</strong>
</div>
{% endif %}
{% if loginerror is defined %}
	<div class="alert alert-error">
		<strong>{{ loginerror }}</strong>
	</div>
{% endif %}

{% if enableregistration == 0 and registrationmode %}
	<div class="alert alert-error">
		<strong>{% trans %}TXT_REGISTRATION_DISABLED_HELP{% endtrans %}</strong>
	</div>
{% endif %}

<div class="order-wrap" style="background: none;">
	<div class="row-fluid row-form">
		<div class="span9">
			<div class="span3">
				<h3 class="normal font20">Posiadam konto</h3>
			</div>
			<div class="span6">
				<form name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}" class="order-wrap">
					<input type="hidden" name="{{ form.submit_name }}" value="1" />
					<fieldset>
						<div class="well well-small">
							<div class="login-form">
								<legend>
									{% trans %}TXT_LOGIN{% endtrans %} <small>{% trans %}TXT_FORM_REQUIRED{% endtrans %}</small>
								</legend>
								{{ forms.input(form.children.login, 'input-xlarge') }}
								{{ forms.password(form.children.password, 'input-xlarge') }}
								{{ forms.checkbox(form.children.autologin, 'input-xlarge') }}
								{{ forms.hidden(form.children.__csrf) }}
								<div class="form-actions form-actions-clean">
									<a href="{{ path('frontend.forgotpassword') }}" title="">{% trans %}TXT_FORGOT_PASSWORD{% endtrans %}</a>
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
					{{ form.javascript }}
				</form>
			</div>
		</div>
	</div>
</div>
{% endblock %}
