{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/productcombination-edit.png" alt=""/>{% trans %}TXT_EDIT_PRODUCTCOMBINATION{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}productcombination" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PRODUCTCOMBINATION_LIST{% endtrans %}" alt="{% trans %}TXT_PRODUCTCOMBINATION_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#productcombination" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}