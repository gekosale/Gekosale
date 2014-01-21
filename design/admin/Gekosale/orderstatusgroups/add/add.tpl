{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.gradient.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/colorpicker.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/status-add.png" alt=""/>{% trans %}TXT_ADD_ORDER_STATUS_GROUPS{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}orderstatusgroups" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_ORDER_STATUS_GROUPS{% endtrans %}" alt="{% trans %}TXT_ORDER_STATUS_GROUPS{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#orderstatusgroups" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#orderstatusgroups" rel="submit[next]" class="button" title="{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}</span></a></li>
	<li><a href="#orderstatusgroups" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}