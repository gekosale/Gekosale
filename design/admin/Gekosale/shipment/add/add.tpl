{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/invoice-add.png" alt=""/>{% trans %}TXT_ADD_SHIPMENT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}invoice" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_VIEW_ORDER_INVOICES{% endtrans %}" alt="{% trans %}TXT_GROUPS_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#shipment" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}