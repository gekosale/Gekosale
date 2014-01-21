{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/dispatchmethod-list.png" alt=""/>{% trans %}TXT_DISPATCHMETHOD_PANE{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_DISPATCHMETHOD{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_DISPATCHMETHOD{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-dispatchmethods"></div>
</div>
<script type="text/javascript">

function editDispatchMethod(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteDispatchMethod(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteDispatchMethod(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'iddispatchmethod',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90,
			visible: false,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
	
	var column_name = new GF_Datagrid_Column({
		id: 'name',
		caption: '{% trans %}TXT_NAME{% endtrans %}',
		appearance: {
			width: 240,
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetNameSuggestions,
		}
	});

	var column_hierarchy = new GF_Datagrid_Column({
		id: 'hierarchy',
		editable: true,
		appearance: {
			width: 40,
		},
		caption: '{% trans %}TXT_HIERARCHY{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
	
	var column_countries = new GF_Datagrid_Column({
		id: 'countries',
		appearance: {
			width: 140,
		},
		caption: '{% trans %}TXT_COUNTRY{% endtrans %}',
	});
		
    var options = {
		id: 'dispatchmethod',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'iddispatchmethod',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllDispatchMethod,
			delete_row: deleteDispatchMethod,
			edit_row: editDispatchMethod,
			update_row: function(sId, oRow) {
				xajax_doAJAXUpdateMethod({
					id: sId,
					hierarchy: oRow.hierarchy
				}, GCallback(function(eEvent) {
					theDatagrid.LoadData();
				}));
			},
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editDispatchMethod
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_hierarchy,
			column_countries,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-dispatchmethods'), options);
		
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}