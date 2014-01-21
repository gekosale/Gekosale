{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.css">
{% endblock %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/xml/xml.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/css/css.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/util/loadmode.js"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-add.png" alt=""/>{% trans %}TXT_ADD_TEMPLATE{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}transmailheader" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_TRANSMAIL_HEADERS_LIST{% endtrans %}" alt="{% trans %}TXT_TRANSMAIL_HEADERS_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#transmailheader" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#transmailheader" rel="submit[next]" class="button" title="{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}</span></a></li>
	<li><a href="#transmailheader" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}