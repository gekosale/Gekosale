{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/product-add.png" alt=""/>{% trans %}TXT_EXCHANGE{% endtrans %}</h2>
<!-- <ul class="possibilities">
	<li><a href="{{ URL }}exchange" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li>
</ul> -->
{{ form }}
{% endblock %}