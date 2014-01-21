{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/productcombination-add.png" alt=""/>{% trans %}TXT_ADD_PRODUCTCOMBINATION{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}productcombination" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PRODUCTCOMBINATION_LIST{% endtrans %}" alt="{% trans %}TXT_PRODUCTCOMBINATION_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#productcombination" rel="submit[next]" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}</span></a></li>
	<li><a href="#productcombination" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}