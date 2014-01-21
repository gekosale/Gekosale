{% extends "layout.tpl" %}
{% block content %}
<h2>Nadawanie paczki InPost</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}inpost/index" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_GROUPS_LIST{% endtrans %}" alt="{% trans %}TXT_GROUPS_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#add_inpost" rel="reset" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li>
	<li><a href="#add_inpost" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}