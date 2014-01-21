{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/status-list.png" alt=""/>{% trans %}TXT_ORDERSTATUS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_ORDERSTATUS{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_ORDERSTATUS{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-orderstatus"></div>
</div>

<script type="text/javascript">

function editOrderstatus(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteOrderstatus(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	if(oRow.def == 1){
		return GError('Nie można usunąć statusu domyślnego');
	}
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteOrderstatus(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function setDefault(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DEFAULT{% endtrans %}';
	var msg = '{% trans %}TXT_SET_DEFAULT{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};

	var func = function(p) {
		return xajax_setDefault(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};	

var theDatagrid;
	 
$(document).ready(function() {
		
   	var action_setDefault = new GF_Action({
		caption: '{% trans %}TXT_SET_DEFAULT{% endtrans %}',
		action: setDefault,
	   	img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
	   	condition: function(oR) { return oR['def'] == '0'; }
	});
	   
	var column_id = new GF_Datagrid_Column({
		id: 'idorderstatus',
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
		
	var column_groupname = new GF_Datagrid_Column({
		id: 'groupname',
		caption: '{% trans %}TXT_GROUP_NAME{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{{ datagrid_filter.groupname }}
			],
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
		id: 'orderstatus',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idorderstatus',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllOrderstatus,
			delete_row: deleteOrderstatus,
			edit_row: editOrderstatus,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editOrderstatus
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_groupname,
			column_adddate
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
			action_setDefault
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
			action_setDefault
		]
    };

	theDatagrid = new GF_Datagrid($('#list-orderstatus'), options);
		
});
</script>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}