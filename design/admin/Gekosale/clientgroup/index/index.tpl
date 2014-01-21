{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/clientgroup-list.png" alt=""/>{% trans %}TXT_CLIENT_GROUPS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_CLIENT_GROUP{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_CLIENT_GROUP{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-clientgroups"></div>
</div>
<script type="text/javascript">

function editClientGroup(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteClientGroup(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteClientGroup(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleClientGroups(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteClientGroup(p.dg, p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};
   
var theDatagrid;
   
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idclientgroup',
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
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetNameSuggestions,
		}
	});
	
	var column_client_count = new GF_Datagrid_Column({
		id: 'clientcount',
		caption: '{% trans %}TXT_CLIENT_COUNT{% endtrans %}',
		appearance: {
			width: 110
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
	
	var options = {
		id: 'clientgroup',
		mechanics: {
			key: 'idclientgroup',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllClientGroup,
			delete_row: deleteClientGroup,
			edit_row: editClientGroup,
			delete_group: deleteMultipleClientGroups,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editClientGroup
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_client_count,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-clientgroups'), options);
		
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}