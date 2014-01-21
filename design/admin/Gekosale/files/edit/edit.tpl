{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/category-list.png" alt=""/>{% trans %}TXT_ADD_FILES{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}files" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_FILES_LIST{% endtrans %}" alt="{% trans %}TXT_FILES_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#edit_files" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}