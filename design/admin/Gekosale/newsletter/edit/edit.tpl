{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-edit.png" alt=""/>{% trans %}TXT_EDIT_NEWSLETTER{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}newsletter" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_NEWSLETTER_LIST{% endtrans %}" alt="{% trans %}TXT_NEWSLETTER_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#newsletter" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#newsletter" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
	<li><a href="#newsletter" rel="submit[send]" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-right-green.png" alt=""/>{% trans %}TXT_SEND{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}
