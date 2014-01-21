{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/contentcategory-edit.png" alt=""/>{% trans %}TXT_EDIT_CONTENT_CATEGORY{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}contentcategory" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_CONTENTCATEGORY_LIST{% endtrans %}" alt="{% trans %}TXT_CONTENTCATEGORY_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#product" class="button show" id="show" rel="show"><span><img src="{{ DESIGNPATH }}_images_panel/icons/datagrid/details.png" alt=""/>{% trans %}TXT_SHOW_IN_SHOP{% endtrans %}</span></a></li>
	<li><a href="#contentcategory" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>

<script type="text/javascript">
function openCategoryEditor(sId) {
	if (sId == undefined || sId == 0) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
	}
	else {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + sId;
	}
};

function openCategoryEditorDuplicate(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
	}
	else {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/duplicate/' + sId;
	}
};

$(document).ready(function() {

	$('#show').click(function(){
		window.open('{{ contentLink }}');
	});
});

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
{% endblock %}