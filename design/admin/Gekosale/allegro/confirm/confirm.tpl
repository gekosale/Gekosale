{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/allegro.js"></script>
{% endblock %}
{% block content %}
{% if errormsg %}
	{% include "allegro/error.tpl" %}
{% else %}

{% if errormessage is defined %}
<script type="text/javascript">
	$(document).ready(function(){
		GError('{{ errormessage }}');
	});
</script>
{% endif %}

<h2>{% trans %}TXT_ALLEGRO_NEW_AUCTION{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}allegro/add" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" /></span></a></li>
	<li><a href="#confirm_allegro" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_ALLEGRO_PUBLISH_AUCTION{% endtrans %}</span></a></li>
</ul>
{{ form }}
<style>
.GForm.tabbed-horizontal .GF_Datagrid {
	width: 1156px;
}
.allegro-category-selector ul.ui-tabs-nav li a {
	font-size: 10px;
}
.GForm > .GBlock > fieldset {
	top: -17px;
	position: relative;
}
.GForm.tabbed-horizontal .form-navigation {
	display: none;
}
</style>
{% endif %}
{% endblock %}