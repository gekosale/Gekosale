{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/invoice-list.png" alt=""/>Integracja Sendingo</h2>
{% if verified is defined and verified == 0 %}
	<ul class="possibilities">
	   <li><a href="{{ URL }}instancemanager" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
	</ul>
	<div class="block">
		<p><img src="{{ DESIGNPATH }}_images_panel/logos/sendingo_logo.png" /></p>
		<p>Twoje konto w systemie WellCommerce nie jest aktywne. Przejdź do <a href="{{ path('admin', {"controller": "instancemanager", "action": "view", "param": "account"}) }}">zarządzania danymi konta</a>. Po wprowadzeniu danych zostaną one zweryfikowane i zostanie udostępniona możliwość automatycznej integracji.</p>
	</div>
{% else %}
	<ul class="possibilities">
	   <li><a href="{{ URL }}instancemanager" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
	   {% if disableNavigation is not defined %}<li><a href="#sendingo" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>{% endif %}
	</ul>
	{% if sendingoError is defined %}
	<div class="block">
		{{ sendingoError }}
	</div>
	{% else %}
		{{ form }}
	{% endif %}
{% if disableNavigation is defined %}
<script>
$(document).ready(function(){
	$('.next').remove();
});
</script>
{% endif %}
{% endif %}
{% endblock %}
