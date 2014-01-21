{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/category-list.png" alt=""/>{% trans %}TXT_CATEGORY_LIST{% endtrans %}</h2>
{% if total > 0 %}
<ul class="possibilities">
	<li><a href="#" id="refresh" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/datagrid/refresh.png" alt=""/>{% trans %}TXT_REFRESH_SEO{% endtrans %}</span></a></li>
</ul>
{% endif %}
<script type="text/javascript">
function openCategoryEditor(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
	}
	else {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + sId;
	}
};
$(document).ready(function() {
	$('#refresh').click(function(){
		return xajax_doAJAXRefreshSeoCategory();
	});
});
</script>
<div class="block">
	{{ tree }}
</div>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}