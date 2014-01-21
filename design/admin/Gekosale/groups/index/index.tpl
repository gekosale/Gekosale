{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/clientgroup-list.png" alt=""/>{% trans %}TXT_ADMINISTRATOR_GROUPS{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_GROUP{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_GROUP{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-groups"></div>
</div>

<script type="text/javascript">

function editGroup(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteGroup(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteGroup(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
var theDatagrid;
	 
$(document).ready(function() {

	var action_removeGroup = new GF_Action({
		caption: '{% trans %}TXT_DELETE{% endtrans %}',
		action: deleteGroup,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/delete.png',
		condition: function(oR) { return oR['usercount'] == 0; }
	});

	var column_id = new GF_Datagrid_Column({
		id: 'idgroup',
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
		appearance: {
			width: 460
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetNameSuggestions,
		}
	});
		
	var column_user_count = new GF_Datagrid_Column({
		id: 'usercount',
		caption: '{% trans %}TXT_USER_COUNT{% endtrans %}',
		appearance: {
			width: 80
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_adddate = new GF_Datagrid_Column({
		id: 'adddate',
		caption: '{% trans %}TXT_ADDDATE{% endtrans %}',
		appearance: {
			width: 140,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
    var options = {
		id: 'group',
		mechanics: {
			key: 'idgroup',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllGroups,
			delete_row: deleteGroup,
			edit_row: editGroup,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editGroup
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_user_count,
			column_adddate,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removeGroup
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removeGroup
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-groups'), options);
		
});
</script>
{% endblock %}