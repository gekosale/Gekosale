{% extends "layout.tpl" %}
{% block content %}
<h2>{% trans %}TXT_VIEW_ORDER_SHIPMENTS{% endtrans %} {{ shipmentTitle }}</h2>
{% if isShipmentSelected > 0 %}
<ul class="possibilities">
	<li><a href="#" id="export" class="button"><span>Wyślij paczki i pobierz protokół</span></a></li>
</ul>
{% endif %}
<div class="block">
	<div id="list-shipment"></div>
</div>

{% if errormessage is defined %}
<script type="text/javascript">
	$(document).ready(function(){
		GError('{{ errormessage }}');
	});
</script>
{% endif %}

<script type="text/javascript">

var theDatagrid;

function viewOrder(dg, id) {
    var oRow = theDatagrid.GetRow(id);
    window.open('{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + oRow.guid + '');
};

function deleteOrder(dg, id) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteShipment(p.id, p.dg);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idshipment',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_orderid = new GF_Datagrid_Column({
		id: 'orderid',
		caption: '{% trans %}TXT_ORDER_ID{% endtrans %}',
		appearance: {
			width: 90,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_adddate = new GF_Datagrid_Column({
		id: 'adddate',
		caption: '{% trans %}TXT_DATE{% endtrans %}',
		appearance: {
			width: 140,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
        
	var column_guid = new GF_Datagrid_Column({
		id: 'guid',
		caption: 'Numer paczki',
		appearance: {
			width: 30,
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});        
		
	var column_packagenumber = new GF_Datagrid_Column({
		id: 'packagenumber',
		caption: 'Numer przesyłki',
		appearance: {
			width: 30,
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});        

	var column_model = new GF_Datagrid_Column({
		id: 'model',
		caption: 'Kurier',
		appearance: {
			width: 70
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{{ datagrid_filter.model }}
			],
		}
	});
			
   	var options = {
		id: 'shipment',
		mechanics: {
			key: 'idshipment'
		},
		event_handlers: {
			load: xajax_LoadAllShipments,
			view_row: viewOrder,
			delete_row: deleteOrder
		},
		columns: [
			column_id,
			column_orderid,
			column_adddate,
			column_guid,
			column_packagenumber,
			{% if isShipmentSelected == 0 %}
			column_model
			{% endif %}
		],
		row_actions: [
			GF_Datagrid.ACTION_VIEW,
		],
	};
    
    theDatagrid = new GF_Datagrid($('#list-shipment'), options);

    $('.GF_Datagrid_Col_adddate .GF_Datagrid_filter_between .from').datepicker({dateFormat: 'yy-mm-dd 00:00:00'});
    $('.GF_Datagrid_Col_adddate .GF_Datagrid_filter_between .to').datepicker({dateFormat: 'yy-mm-dd 23:59:59'});

    
    $('#export').click(function(){
    	var selected = theDatagrid.GetSelected();
    	if(selected.length){
			window.open('{{ URL }}{{ CURRENT_CONTROLLER }}/confirm/'+ Base64.encode(JSON.stringify(selected)));
    	}else{
    		var title = '{% trans %}TXT_SHIPMENT_EXPORT{% endtrans %}';
			var msg = '{% trans %}ERR_EMPTY_SHIPMENT_SELECTED_LIST{% endtrans %}';
			var params = {};
			var func = function(p) {

			};
	    	new GMessage(title, msg);
    	}
		return false;
    });
});
</script>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}