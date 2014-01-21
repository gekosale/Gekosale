{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.gradient.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/colorpicker.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/configuration-list.png" alt=""/>{% trans %}TXT_GLOBAL_CONFIGURATION{% endtrans %}</h2>
<ul class="possibilities">
	<!-- <li><a href="#globalsettings" rel="reset" class="button reset"><span>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#globalsettings" rel="submit" class="button continue"><span>{% trans %}TXT_SAVE_AND_CONTINUE{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}