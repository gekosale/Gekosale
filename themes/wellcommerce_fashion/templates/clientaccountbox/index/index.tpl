{% extends "layoutbox.tpl" %}
{% block content %}
 {% if client is not empty %}
<nav class="category-nav well">
	<h1>{{ box.heading }}</h1>
    <ul class="nav nav-pills nav-stacked">
    	<li {% if CURRENT_CONTROLLER == 'clientsettings' %}class="active"{% endif %}><a href="{{ path('frontend.clientsettings') }}">{% trans %}TXT_SETTINGS{% endtrans %}</a></li>
		<li {% if CURRENT_CONTROLLER == 'clientorder' %}class="active"{% endif %}><a href="{{ path('frontend.clientorder') }}">{% trans %}TXT_ORDERS{% endtrans %}</a></li>
		<li {% if CURRENT_CONTROLLER == 'clientaddress' %}class="active"{% endif %}><a href="{{ path('frontend.clientaddress') }}">{% trans %}TXT_CLIENT_ADDRESS{% endtrans %}</a></li>
		<li {% if CURRENT_CONTROLLER == 'wishlist' %}class="active"{% endif %}><a href="{{ path('frontend.wishlist') }}">{% trans %}TXT_CLIPBOARD{% endtrans %}</a></li>
    </ul>
</nav>
{% else %}
<nav class="category-nav well">
	<h1>{{ box.heading }}</h1>
    <ul class="nav nav-pills nav-stacked">
    	<li><a href="{{ path('frontend.clientlogin') }}">{% trans %}TXT_LOGIN_TO_YOUR_ACCOUNT{% endtrans %}</a></li>
		<li><a href="{{ path('frontend.registration') }}">{% trans %}TXT_REGISTER{% endtrans %}</a></li>
    </ul>
</nav>
{% endif %}
{% endblock %}