{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/invoice-list.png" alt=""/>Faktury i płatności</h2>
<ul class="possibilities">
   <li><a href="{{ URL }}instancemanager" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
</ul>
{{ form }}
<style>
.navigation {
	display: none;
}
</style>
{% endblock %}