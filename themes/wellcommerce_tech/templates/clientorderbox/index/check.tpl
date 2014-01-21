{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
<article class="article">
	<h1>{% trans %}TXT_ORDER_STATUS{% endtrans %}</h1>
	{% if status is defined %}
		{% if status is not null %}
	    <div class="alert alert-block alert-info">
			Aktualny status zamówienia #{{ status.orderid }}: <strong>{{ status.name }}</strong>
		</div>
		{% else %}
		<div class="alert alert-block alert-error">
			Podałeś niepoprawne dane. Nie jest możliwe sprawdzenie statusu zamówienia.
		</div>
		{% endif %}
	{% endif %}
	<form class="well" name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}">
		<input type="hidden" name="{{ form.submit_name }}" value="1" />
		<div class="alert alert-block alert-info">
			Jeśli chcesz uzyskać więcej informacji - <a href="{{ path('frontend.clientlogin') }}"><strong>zaloguj się</strong></a> korzystając z danych konta utworzonego przy składaniu zamówienia
		</div>
		<fieldset>
			<div class="row-fluid">
				<div class="span5">
					{{ forms.input(form.children.email, 'span12') }}
				</div>
				<div class="span5">
					{{ forms.input(form.children.orderid, 'span12') }}
				</div>
			</div>
			{{ forms.hidden(form.children.__csrf) }}
			<div class="form-actions form-actions-clean">
				<div class="row-fluid">
					<button type="submit" class="btn btn-large btn-primary">{% trans %}TXT_CHECK_STATUS{% endtrans %}</button>
				</div>
			</div>
		</fieldset>
		{{ form.javascript }}
	</form>

</article>
{% endblock %}