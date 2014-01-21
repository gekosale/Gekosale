{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/send.png" alt=""/>{% trans %}TXT_SEND_NOTIFICATION{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}substitutedservicesend" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_RULES_CATALOG_LIST{% endtrans %}" alt="{% trans %}TXT_RULES_CATALOG_LIST{% endtrans %}"/></span></a></li>
</ul>

<div class="column wide-collapsed">
	{{ form }}
</div>
{% endblock %}