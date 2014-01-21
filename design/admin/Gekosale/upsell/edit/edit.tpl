{% extends "layout.tpl" %}
{% block content %}
<h2>{% trans %}TXT_EDIT_UPSELL{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}upsell" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_UPSELL_LIST{% endtrans %}" alt="{% trans %}TXT_UPSELL_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#upsell" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#upsell" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}