{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
<article class="article category-list">
	<h1>{{ box.heading }}</h1>
	{% if emailerror is defined %}
	<div class="alert alert-error">
		{{ emailerror }}
	</div>
	{% endif %} 
	{% if sendPasswd is defined %}
	<div class="alert alert-success">
		{{ sendPasswd }}
	</div>
	{% endif %} 
	{% if emailerror is not defined and sendPasswd is not defined %}
	<div class="alert alert-info">
		{% trans %}TXT_PASSWORD_RESET_HELP{% endtrans %}
	</div>
	{% endif %}
	 
	<form name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}">
		<input type="hidden" name="{{ form.submit_name }}" value="1" />
		<fieldset>
			<div class="well well-small">
				<div class="login-form">
					<legend>
						{% trans %}TXT_LOGIN_FORM_RESET_PASSWORD{% endtrans %}
					</legend>
					{{ forms.input(form.children.email, 'input-xlarge') }}
					{{ forms.hidden(form.children.__csrf) }}
					<div class="form-actions form-actions-clean">
						<button type="submit" class="btn btn-large btn-primary">{% trans %}TXT_SEND{% endtrans %}</button>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</fieldset>
		{{ form.javascript }}
	</form>
</article>
{% endblock %} 