{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
{% if emailerror is defined %}
<script>
GError('{{ emailerror }}','');
</script>
{% endif %} 
{% if sendPasswd is defined %}
<script>
GMessage('{{ sendPasswd }}','');
</script>
{% endif %} 

<article class="article category-list">
	<h1>{{ box.heading }}</h1>
	<form name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}">
		<input type="hidden" name="{{ form.submit_name }}" value="1" />
		<fieldset>
			<div class="well well-small">
				<div class="login-form">
					<legend>
						{% trans %}TXT_PASSWORD_FORGOT{% endtrans %}
					</legend>
					{{ forms.password(form.children.newpassword, 'input-xlarge') }}
					{{ forms.password(form.children.confirmpassword, 'input-xlarge') }}
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