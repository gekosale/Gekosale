{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/status-list.png" alt=""/>{% trans %}TXT_AVAILABLITY{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-availablity"></div>
</div>

<script type="text/javascript">
function editAvailablity(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteAvailablity(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteAvailablity(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idavailablity',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_name = new GF_Datagrid_Column({
		id: 'name',
		caption: '{% trans %}TXT_NAME{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
		
    var options = {
		id: 'availablity',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idavailablity',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllAvailablity,
			delete_row: deleteAvailablity,
			edit_row: editAvailablity,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editAvailablity
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-availablity'), options);
});
</script>
{% endblock %}