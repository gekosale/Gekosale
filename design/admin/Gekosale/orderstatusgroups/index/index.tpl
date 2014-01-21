{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/order-list.png" alt=""/>{% trans %}TXT_ORDER_STATUS_GROUPS{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_ORDER_STATUS_GROUPS{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_ORDER_STATUS_GROUPS{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-orderstatusgroups"></div>
</div>

<script type="text/javascript">
	 
function editOrderStatusGroups(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteOrderStatusGroups(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteOrderStatusGroups(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
var theDatagrid;

function processOrderStatusGroups(row) {

	return {
		idorderstatusgroups: row.idorderstatusgroups,
		name: row.name,
		adddate: row.adddate,
		colour: '<span style="margin: 0 auto;width: 16px; height: 16px; display: block; background-color: #' + row.colour + ';"></span>',
	};
};

$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idorderstatusgroups',
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
		}
	});
	
	var column_colour = new GF_Datagrid_Column({
		id: 'colour',
		caption: '{% trans %}TXT_COLOUR{% endtrans %}',
		appearance: {
			width: 90,
		},
	});

	var column_adddate = new GF_Datagrid_Column({
		id: 'adddate',
		caption: '{% trans %}TXT_ADDDATE{% endtrans %}',
		appearance: {
			width: 150,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

    var options = {
		id: 'orderstatusgroups',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idorderstatusgroups',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllOrderStatusGroups,
			process: processOrderStatusGroups,
			delete_row: deleteOrderStatusGroups,
			edit_row: editOrderStatusGroups,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editOrderStatusGroups
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_colour,
			column_adddate
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		],
    };
    
    theDatagrid = new GF_Datagrid($('#list-orderstatusgroups'), options);
		
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}

