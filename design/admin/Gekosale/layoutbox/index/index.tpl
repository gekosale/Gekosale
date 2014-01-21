{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/layoutbox-list.png" alt=""/>{% trans %}TXT_LAYOUT_BOXES{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt="{% trans %}TXT_LAYOUT_BOX_ADD{% endtrans %}"/>{% trans %}TXT_LAYOUT_BOX_ADD{% endtrans %}</span></a></li>
</ul>
<script type="text/javascript">
function openLayoutBoxEditor(sId) {
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
{% block sticky %}
{% include sticky %}
{% endblock %}