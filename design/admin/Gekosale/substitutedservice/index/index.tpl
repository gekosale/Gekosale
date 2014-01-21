{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-list.png" alt=""/>{% trans %}TXT_SUBSTITUTED_SERVICE{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-substitutedservices"></div>
</div>

<script type="text/javascript">

function sendSubstitutedService(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/confirm/' + id + '';
};
   
function editSubstitutedservice(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};
   
function deleteSubstitutedservice(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteSubstitutedservice(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleSubstitutedservice(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteSubstitutedservice(p.dg, p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};
	
var theDatagrid;
	 
$(document).ready(function() {

	var column_id = new GF_Datagrid_Column({
		id: 'idsubstitutedservice',
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

	var action_sendSubstitutedService = new GF_Action({
		caption: '{% trans %}TXT_SEND{% endtrans %}',
		action: sendSubstitutedService,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/send.png',
	 });
		 
   	var options = {
		id: 'idsubstitutedservice',
		mechanics: {
			key: 'idsubstitutedservice',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllSubstitutedservice,
			delete_row: deleteSubstitutedservice,
			edit_row: editSubstitutedservice,
			delete_group: deleteMultipleSubstitutedservice,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editSubstitutedservice
			{% endif %}
		},
		columns: [
			column_id,
			column_name
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_sendSubstitutedService,
			GF_Datagrid.ACTION_DELETE
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		]
	};
    
    theDatagrid = new GF_Datagrid($('#list-substitutedservices'), options);
    
});
</script>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}