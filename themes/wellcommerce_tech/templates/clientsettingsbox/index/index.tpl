{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
<article class="article">
	<h1>{% trans %}TXT_SETTINGS{% endtrans %}</h1>
	{% if duplicateMailError is defined %}
	<div class="alert scroll alert-error">
		<strong>{{ duplicateMailError }}</strong>
	</div>
	{% endif %}
	{% if registrationok is defined %}
	<div class="alert scroll alert-success">
		<strong>{{ registrationok }}</strong>
	</div>
	{% endif %}
	<form class="form-horizontal well" name="{{ formEmail.name }}" id="{{ formEmail.name }}" method="{{ formEmail.method }}" action="{{ formEmail.action }}">
		<input type="hidden" name="{{ formEmail.submit_name }}" value="1" />
		<fieldset>
			<legend>
				{% trans %}TXT_CONTACT_DATA{% endtrans %}
			</legend>
			<br />
			{% if settingsSaved is defined %}
			<div class="alert scroll alert-success">
				<strong>{{ settingsSaved }}</strong>
			</div>
			{% else %}
			<div class="alert scroll alert-info">
				Zmieniając adres e-mail zmieni się również Twój login do sklepu. Po zmianie zostaniesz wylogowany i będzie konieczne ponowne zalogowanie w sklepie.
			</div>
			{% endif %}
			{{ forms.input(formEmail.children.email, 'input-xlarge') }}
			{{ forms.input(formEmail.children.phone, 'input-xlarge') }}
			{{ forms.input(formEmail.children.phone2, 'input-xlarge') }}
			{{ forms.hidden(formEmail.children.__csrf) }}
			<div class="form-actions form-actions-clean">
				<button type="submit" class="btn btn-large btn-primary marginlt20">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
			</div>
		</fieldset>
		{{ formEmail.javascript }}
	</form>

	{% if changedPasswd is defined %}
	<div class="alert alert-success">
		{{ changedPasswd }}
	</div>
	{% endif %}
	<form class="form-horizontal well" name="{{ formPass.name }}" id="{{ formPass.name }}" method="{{ formPass.method }}" action="{{ formPass.action }}">
		<input type="hidden" name="{{ formPass.submit_name }}" value="1" />
		<fieldset>
			<legend>
				{% trans %}TXT_CHANGE_PASSWORD{% endtrans %}
			</legend>
			{{ forms.password(formPass.children.password, 'input-xlarge') }}
			{{ forms.password(formPass.children.newpassword, 'input-xlarge') }}
			{{ forms.password(formPass.children.confirmpassword, 'input-xlarge') }}
			{{ forms.hidden(formPass.children.__csrf) }}
			<div class="form-actions form-actions-clean">
				<button type="submit" class="btn btn-large btn-primary marginlt20">{% trans %}TXT_SAVE_CHANGES{% endtrans %}</button>
			</div>
		</fieldset>
		{{ formPass.javascript }}
	</form>



</article>
<div class="head-block">
	<span class="font">Oferta dla Ciebie</span>
</div>
<div id="recommendations">
	{{ recommendations(4) }}
</div>
{% endblock %}
