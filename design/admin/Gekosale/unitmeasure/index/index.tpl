{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/unitmeasure-list.png" alt=""/>{% trans %}TXT_UNIT_MEASURES_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_UNIT_MEASURE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_UNIT_MEASURE{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-unitmeasures"></div>
</div>

<script type="text/javascript">
function editUnitMeasure(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};
   
function deleteUnitMeasure(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteUnitMeasure(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idunitmeasure',
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
			type: GF_Datagrid.FILTER_INPUT
		}
	});

	var options = {
		id: 'unitmeasure',
		mechanics: {
			key: 'idunitmeasure'
		},
		event_handlers: {
			load: xajax_LoadAllUnitMeasure,
			delete_row: deleteUnitMeasure,
			edit_row: editUnitMeasure,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editUnitMeasure
			{% endif %}
		},
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		],
		columns: [
			column_id,
			column_name
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		],
	};

    theDatagrid = new GF_Datagrid($('#list-unitmeasures'), options);
    
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}