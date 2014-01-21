{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
<a href="#reccomend-form" data-toggle="modal"><span>{% trans %}TXT_SEND_RECOMMENDATION{% endtrans %}</span></a>

<div id="reccomend-form" class="modal fade hide">

	<div class="modal-body">
		<div class="row-fluid row-form">
			<form name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}">
				<fieldset>
					<div class="row-fluid">
						<legend>
							{% trans %}TXT_SEND_RECOMMENDATION{% endtrans %} <small>*{% trans %}TXT_REQUIRED_FIELD{% endtrans %}</small>
						</legend>

						<input type="hidden" name="{{ form.submit_name }}" value="1" />

						{{ forms.input(form.children.fromname, 'input-large span12') }}

						{{ forms.input(form.children.fromemail, 'input-large span12') }}

						{{ forms.input(form.children.friendemail, 'input-large span12') }}

						{{ forms.textarea(form.children.content, 'input-large span12') }}

						{{ forms.hidden(form.children.__csrf) }}
					</div>
				</fieldset>
				{{ form.javascript }}
			</form>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">{% trans %}Close{% endtrans %}</a>
		<button type="submit" class="btn btn-primary" onclick="$('#{{ form.name }}').submit();">{% trans %}TXT_SEND{% endtrans %}</button>
	</div>
</div>

{% endblock %}