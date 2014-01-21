{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/rulescart-list.png" alt=""/>{% trans %}TXT_INTEGRATION_TERMS{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}integration" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_INTEGRATION{% endtrans %}" alt="{% trans %}TXT_INTEGRATION{% endtrans %}"/></span></a></li>
</ul>
{{ form }}
{% endblock %}