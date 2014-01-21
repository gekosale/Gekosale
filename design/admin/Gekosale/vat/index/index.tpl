{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/vat-list.png" alt=""/>{% trans %}TXT_VAT_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_VAT{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_VAT{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-VAT"></div>
</div>

<script type="text/javascript">
  
function editVAT(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteVAT(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteVAT(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idvat',
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
			width: 140
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
		
	var column_value = new GF_Datagrid_Column({
		id: 'value',
		caption: '{% trans %}TXT_VALUE{% endtrans %}',
		appearance: {
			width: 140
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_product_count = new GF_Datagrid_Column({
		id: 'productcount',
		caption: '{% trans %}TXT_PRODUCT_COUNT{% endtrans %}',
		appearance: {
			width: 140
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
    var options = {
		id: 'VAT',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idvat',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllVAT,
			delete_row: deleteVAT,
			edit_row: editVAT,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editVAT
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_value,
			column_product_count,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		]
	};
    
    theDatagrid = new GF_Datagrid($('#list-VAT'), options);
		
});

</script>
{% endblock %}