{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/contentcategory-list.png" alt=""/>{% trans %}TXT_CONTENTCATEGORY_LIST{% endtrans %}</h2>

<script type="text/javascript">
function openCategoryEditor(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
	}
	else {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + sId;
	}
};
</script>

<div class="block">
	{{ tree }}
</div>
{% endblock %}