{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.css">
{% endblock %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.gradient.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/colorpicker.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/xml/xml.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/css/css.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/util/loadmode.js"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/pagescheme-edit.png" alt=""/>{% trans %}TXT_PAGE_SCHEME_EDIT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}pagescheme" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PAGE_SCHEME_TEMPLATES_LIST{% endtrans %}" alt="{% trans %}TXT_PAGE_SCHEME_TEMPLATES_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#pagescheme" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#pagescheme" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
	<li><a href="#pagescheme" rel="submit[continue]" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_CONTINUE{% endtrans %}</span></a></li>
</ul>

<script type="text/javascript">
function openPageSchemeEditor(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/';
	}
	else {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + sId;
	}
};
</script>

<div class="layout-two-columns">
	<div class="column narrow-collapsed">
		<div class="block">
			{{ tree }}
		</div>
	</div>
	<div class="column wide-collapsed">
		{{ form }}
	</div>
</div>
<style>
.GForm .wide span.repetition {
	display: block;
	margin-bottom: 5px;
	margin-left: 0px;
}
.CodeMirror-scroll {
	min-height: 750px;
	overflow-y: auto;
	overflow-x: auto;
	width: 710px;
}
.column.wide-collapsed .GForm .GF_Datagrid {
width: 472px;
}
</style>
{% endblock %}



{% block sticky %}
{% include sticky %}
{% endblock %}