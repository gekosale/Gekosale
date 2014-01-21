{% import "forms.tpl" as forms %}
{% extends "layoutbox.tpl" %}
{% block content %}
{% if sendContact is defined %}
<div class="alert alert-success">
	<strong>{{ sendContact }}</strong>
</div>
{% endif %}

<article class="article">
	{% for contact in contactList %}
	<h1>{{ contact.name }}</h1>
	<div class="row-fluid marginbt50">
		<div class="span1">&nbsp;</div>
		<div class="span3">
			<h4>{% trans %}TXT_ADDRESS{% endtrans %}</h4>
			{{ contact.street }} {{ contact.streetno }} {{ contact.placeno }}<br>
			{{ contact.postcode }} {{ contact.placename }}
		</div>
		<div class="span4">
			<h4>{% trans %}TXT_PHONE{% endtrans %}</h4>
			<div class="phone">
				<h3 class="font">{{ contact.phone }}</h3>
				{% if contact.businesshours != '' %} <span>{{ contact.businesshours }}</span> {% endif %}
			</div>
		</div>
		<div class="span4">
			<h4>{% trans %}TXT_EMAIL{% endtrans %}</h4>
			<div class="email">
				<a href="#" title="">{{ contact.email }}</a>
			</div>
		</div>
	</div>
	{% endfor %}
	<h1>{{ box.heading }}</h1>

	{{ content.content }}

	<form class="well" name="{{ form.name }}" id="{{ form.name }}" method="{{ form.method }}" action="{{ form.action }}">
		<input type="hidden" name="{{ form.submit_name }}" value="1" />
		<fieldset>
			<div class="row-fluid">
				<div class="span5">
					{{ forms.input(form.children.firstname, 'span12') }}
				</div>
				<div class="span5">
					{{ forms.input(form.children.surname, 'span12') }}
				</div>
			</div>
			<div class="row-fluid">
				<div class="span5">
					{{ forms.input(form.children.email, 'span12') }}
				</div>
				<div class="span5">
					{{ forms.input(form.children.phone, 'span12') }}
				</div>
			</div>
			{% if productid is not defined %}
			<div class="row-fluid">
				<div class="span10">
					{{ forms.input(form.children.topic, 'span12') }}
				</div>
			</div>
			{% endif %}
			<div class="row-fluid">
				<div class="span10">
					{{ forms.select(form.children.contactsubject, 'span12') }}
				</div>
			</div>
			<div class="row-fluid">
				<div class="span10">
					{{ forms.textarea(form.children.content, 'span12') }}
				</div>
			</div>
			{{ forms.hidden(form.children.__csrf) }}
			<div class="form-actions form-actions-clean">
				<div class="row-fluid">
					<button type="submit" class="btn btn-large btn-primary">{% trans %}TXT_SEND_MESSAGE{% endtrans %}</button>
				</div>
			</div>
		</fieldset>
		{{ form.javascript }}
	</form>
</article>
{% endblock %}
