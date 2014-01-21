{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/productcombination-list.png" alt=""/>{% trans %}TXT_PRODUCTCOMBINACTIONS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_PRODUCTCOMBINACTION{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-productcombinations"></div>
</div>

<script type="text/javascript">
function editCombination(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteCombination(dg, id) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteCombination(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleCombinations(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteCombination(p.dg, p.ids);
	};
    new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idcombination',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90
		}, 
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_products = new GF_Datagrid_Column({
		id: 'products',
		caption: '{% trans %}TXT_PRODUCTS{% endtrans %}',
		appearance: {
			align: GF_Datagrid.ALIGN_LEFTs
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
		
	var column_discount = new GF_Datagrid_Column({
		id: 'discount',
		caption: '{% trans %}TXT_DISCOUNT{% endtrans %}',
		appearance: {
			width: 130
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
    var options = {
		id: 'combination',
		mechanics: {
			key: 'idcombination'
		},
		event_handlers: {
			load: xajax_LoadAllCombination,
			delete_row: deleteCombination,
			edit_row: editCombination,
			delete_group: deleteMultipleCombinations
		},
		columns: [
			column_id,
			column_products,
			column_discount,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
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
    
    theDatagrid = new GF_Datagrid($('#list-productcombinations'), options);
		
});
</script>
{% endblock %}