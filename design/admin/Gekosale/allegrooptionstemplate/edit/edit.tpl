{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/allegro.js"></script>
{% endblock %}
{% block content %}
{% if errormsg %}
	{% include "allegro/error.tpl" %}
{% else %}
<h2><img src="{{ DESIGNPATH }}_images_panel/logos/logo_allegro.jpg" alt=""/>Edycja szablonu opcji Allegro</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}allegrooptionstemplate/index" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_OPTIONS_TEMPLATES_LIST{% endtrans %}" alt="{% trans %}TXT_USER_TEMPLATES_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#allegrooptionstemplate" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endif %}
{% endblock %}