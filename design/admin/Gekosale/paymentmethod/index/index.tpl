{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/paymentmethod-list.png" alt=""/>{% trans %}TXT_PAYMENTMETHOD_PANE{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span>{% trans %}TXT_ADD_PAYMENTMETHOD{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-paymentmethod"></div>
</div>
<script type="text/javascript">

function editPaymentMethod(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deletePaymentMethod(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeletePaymentMethod(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
function enablePaymentmethod(dg, id) {
	xajax_enablePaymentmethod(dg, id);
};
   	 
function disablePaymentmethod(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DISABLE{% endtrans %}';
	var msg = '{% trans %}TXT_DISABLE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_disablePaymentmethod(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};	
	 
function enablePaymentmethod(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_ENABLE{% endtrans %}';
	var msg = '{% trans %}TXT_ENABLE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_enablePaymentmethod(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
	 
$(document).ready(function() {

	var action_enablePaymentmethod= new GF_Action({
		caption: '{% trans %}TXT_ENABLE{% endtrans %}',
		action: enablePaymentmethod,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
			condition: function(oR) { return oR['active'] == '0'; }
		 });
		 
	var action_disablePaymentmethod= new GF_Action({
		caption: '{% trans %}TXT_DISABLE{% endtrans %}',
		action: disablePaymentmethod,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/off.png',
		condition: function(oR) { return oR['active'] == '1'; }
	});
	
	var column_id = new GF_Datagrid_Column({
		id: 'idpaymentmethod',
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
			width: 340,
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
	
	var column_controller = new GF_Datagrid_Column({
		id: 'controller',
		caption: '{% trans %}TXT_PAYMENT_CONTROLLER{% endtrans %}',
		appearance: {
			width: 140,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetControllerSuggestions,
		}
	});
	
    var options = {
		id: 'paymentmethod',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idpaymentmethod',
    		rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllPaymentMethod,
			delete_row: deletePaymentMethod,
			edit_row: editPaymentMethod,
			update_row: function(sId, oRow) {
				xajax_doAJAXUpdateMethod({
					id: sId,
					hierarchy: oRow.hierarchy
				}, GCallback(function(eEvent) {
					theDatagrid.LoadData();
				}));
			},
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editPaymentMethod
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_hierarchy,
			column_controller,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
			action_enablePaymentmethod,
			action_disablePaymentmethod
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
			action_enablePaymentmethod,
			action_disablePaymentmethod
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-paymentmethod'), options);
		
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}