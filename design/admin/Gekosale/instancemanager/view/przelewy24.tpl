{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/invoice-list.png" alt=""/>Integracja Przelewy24.pl</h2>
{% if verified is defined and verified == 0 %}
	<ul class="possibilities">
	   <li><a href="{{ URL }}instancemanager" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
	</ul>
	<div class="block">
		<p><img src="{{ DESIGNPATH }}_images_panel/logos/przelewy24_logo.png" /></p>
		<p>Twoje konto w systemie WellCommerce nie jest aktywne. Przejdź do <a href="{{ path('admin', {"controller": "instancemanager", "action": "view", "param": "account"}) }}">zarządzania danymi konta</a>. Po wprowadzeniu danych zostaną one zweryfikowane i zostanie udostępniona możliwość automatycznej integracji.</p>
	</div>
{% else %}
	<ul class="possibilities">
	   <li><a href="{{ URL }}instancemanager" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
	   <li><a href="#przelewy24" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
	</ul>
	{% if p24error is defined %}
	<div class="block">
		{{ p24error }}
	</div>
	{% else %}
		{{ form }}
	{% endif %}
{% endif %}
{% endblock %}
