{% extends "layout.tpl" %}
{% block content %}
{% if errormsg %}
	{% include "allegro/error.tpl" %}
{% else %}
<h2>Szablony opcji Allegro</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span>Dodaj nowy szablon</a></li>
</ul>
<div class="block">
	<div id="list-allegrooptionstemplate"></div>
</div>

<script type="text/javascript">
function deleteAllegrooptionstemplate(dg, id) {
	var topic = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteAllegrooptionstemplate(p.dg, p.id);
	};
    new GF_Alert(topic, msg, func, true, params);
};

function editAllegrooptionstemplate(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

var theDatagrid;

$(document).ready(function() {

	var column_id = new GF_Datagrid_Column({
		id: 'idallegrooptionstemplate',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_name = new GF_Datagrid_Column({
		id: 'name',
		caption: '{% trans %}TXT_NAME{% endtrans %}',
	});

   	var options = {
		id: 'allegrooptionstemplate',
		mechanics: {
			key: 'idallegrooptionstemplate'
		},
		event_handlers: {
			load: xajax_LoadAllAllegrooptionstemplate,
			delete_row: deleteAllegrooptionstemplate,
			edit_row: editAllegrooptionstemplate,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editAllegrooptionstemplate
			{% endif %}
		},
		columns: [
			column_id,
			column_name
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		],
    };

   	theDatagrid = new GF_Datagrid($('#list-allegrooptionstemplate'), options);

});
</script>
{% endif %}
{% endblock %}