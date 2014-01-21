{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/client-list.png" alt=""/>{% trans %}TXT_CLIENTS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_CLIENT{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_CLIENT{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-clients"></div>
</div>
<script type="text/javascript">
function editClient(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteClient(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteClient(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleClients(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteClient(p.dg, p.ids);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function enableClient(dg, id) {
	xajax_enableClient(dg, id);
};
	 
function disableClient(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DISABLE{% endtrans %}';
	var msg = '{% trans %}TXT_DISABLE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_disableClient(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};	
	 
function enableClient(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_ENABLE{% endtrans %}';
	var msg = '{% trans %}TXT_ENABLE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_enableClient(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
   
$(document).ready(function() {
		
	var action_enableClient = new GF_Action({
		caption: '{% trans %}TXT_ENABLE_CLIENT{% endtrans %}',
		action: enableClient,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/deactivate.png',
		condition: function(oR) { return oR['disable'] != '0'; }
	});
		 
	var action_disableClient = new GF_Action({
		caption: '{% trans %}TXT_DISABLE_CLIENT{% endtrans %}',
		action: disableClient,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/activate.png',
		condition: function(oR) { return oR['disable'] == '0'; }
	});
		 
	var column_id = new GF_Datagrid_Column({
		id: 'idclient',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_clientorder = new GF_Datagrid_Column({
		id: 'clientorder',
		caption: '{% trans %}TXT_CLIENTORDER_VALUE{% endtrans %}',
		appearance: {
			width: 40,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_firstname = new GF_Datagrid_Column({
		id: 'firstname',
		caption: '{% trans %}TXT_FIRSTNAME{% endtrans %}',
		appearance: {
			width: 200
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetFirstnameSuggestions,
		}
	});
	
	var column_surname = new GF_Datagrid_Column({
		id: 'surname',
		caption: '{% trans %}TXT_SURNAME{% endtrans %}',
		appearance: {
			width: 200
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetSurnameSuggestions,
		}
	});
	
	var column_email = new GF_Datagrid_Column({
		id: 'email',
		caption: '{% trans %}TXT_EMAIL{% endtrans %}',
		appearance: {
			width: 180,
			visible: false
		}
	});
		
	var column_phone = new GF_Datagrid_Column({
		id: 'phone',
		caption: '{% trans %}TXT_PHONE{% endtrans %}',
		appearance: {
			width: 110,
			visible: false
		}
	});

	var column_phone2 = new GF_Datagrid_Column({
		id: 'phone2',
		caption: '{% trans %}TXT_ADDITIONAL_PHONE{% endtrans %}',
		appearance: {
			width: 110,
			visible: false
		}
	});
	
	var column_group = new GF_Datagrid_Column({
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
		
	var column_view = new GF_Datagrid_Column({
		id: 'view',
		caption: '{% trans %}TXT_LAYER{% endtrans %}',
		appearance: {
			width: 150
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{{ datagrid_filter.view }}
			],
		}
	});
		
    var options = {
		id: 'client',
		mechanics: {
			key: 'idclient',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllClient,
			delete_row: deleteClient,
			edit_row: editClient,
			delete_group: deleteMultipleClients,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editClient
			{% endif %}
		},
		columns: [
			column_id,
			column_surname,
			column_firstname,
			column_group,
			column_email,
			column_phone,
			column_phone2,
			column_adddate,
			column_clientorder,
			column_view
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_enableClient,
			action_disableClient,
			GF_Datagrid.ACTION_DELETE
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_enableClient,
			action_disableClient,
			GF_Datagrid.ACTION_DELETE
		]
	};
    
    theDatagrid = new GF_Datagrid($('#list-clients'), options);
		
});
</script>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}