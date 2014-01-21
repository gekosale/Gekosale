{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/updater.png" alt=""/>{% trans %}TXT_EXCHANGE{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="#exchange" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>

{{ form }}
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}