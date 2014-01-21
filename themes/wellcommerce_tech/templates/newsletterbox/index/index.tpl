{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
{% if errlink is defined or activelink is defined or inactivelink is defined %}
	{% if errlink  == 1 %}
		<div class="alert alert-error">
			<strong>{% trans %}TXT_INVALID_LINK{% endtrans %}</strong>
		</div>
	{% elseif inactivelink == 1 %}
		<div class="alert alert-success">
			<strong>{% trans %}TXT_DELETE_CLIENT_FROM_NEWSLETTER{% endtrans %}</strong>
		</div>
	{% else %}
		<div class="alert alert-success">
			<strong>{% trans %}TXT_CLIENT_REGISTRATION_NEWSLETTER{% endtrans %}</strong>
		</div>
	{% endif %}
{% else %}

<article class="article">
	<h1>{% trans %}TXT_NEWSLETTER_INFO_FRONTEND{% endtrans %}</h1>
	{% if signup_error is defined %}
	<div class="alert alert-error">
		<strong>{{ signup_error }}</strong>
	</div>
	{% endif %}
	{% if signup_success is defined %}
	<div class="alert alert-success">
		<strong>{{ signup_success }}</strong>
	</div>
	{% endif %}
	<form name="{{ newsletter.name }}" id="{{ newsletter.name }}" method="{{ newsletter.method }}" action="{{ newsletter.action }}">
		<input type="hidden" name="{{ newsletter.submit_name }}" value="1" />
		<fieldset>
			<div class="well well-small well-clean">
				{{ forms.radio(newsletter.children.action, 'input-large') }}
				{{ forms.input(newsletter.children.email, 'input-large') }}
				<div id="confirm" class="collapse in">
				{{ forms.checkbox(newsletter.children.confirmterms) }}
				</div>
				{{ forms.hidden(newsletter.children.__csrf) }}
			</div>
			<div class="form-actions form-actions-clean">
				<button type="submit" class="btn btn-primary btn-large"><i class="icon-envelope icon-white"></i> Zatwierdź</button>
			</div>
		</fieldset>
		{{ newsletter.javascript }}
	</form>
</article>
<script type="text/javascript">
$(document).ready(function(){
	$("#{{ newsletter.name }} input[name='action']").unbind('change').bind('change', function(){
		$('#confirm').collapse($('#newsletter_action_1').is(':checked') ? 'show' : 'hide');
	});
});
</script>
{% endif %}
{% endblock %}