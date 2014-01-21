{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/subpagelayout-list.png" alt=""/>{% trans %}TXT_SUBPAGE_LAYOUTS{% endtrans %}</h2>
<script type="text/javascript">
function openSubpageEditor(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/';
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