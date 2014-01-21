{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/promotion-list.png" alt=""/>{% trans %}TXT_PRODUCT_PROMOTION_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_PROMOTION{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_PROMOTION{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-productnews"></div>
</div>

<script type="text/javascript">
function editProductPromotion (dg, id) {
	location.href = '{{ URL }}product/edit/' + id + '';
};
   
function deleteProductPromotion(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteProductPromotion(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleProductPromotion(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteProductPromotion(p.dg,p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 

var theDatagrid;
	 
$(document).ready(function() {
   
	var column_id = new GF_Datagrid_Column({
		id: 'idproduct',
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
		caption: '{% trans %}TXT_PRODUCT{% endtrans %}',
		appearance: {
			width: 150
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});

	var column_category = new GF_Datagrid_Column({
		id: 'categoriesname',
		caption: '{% trans %}TXT_CATEGORY{% endtrans %}',
		appearance: {
			width: 150
		},
		filter: {
			type: GF_Datagrid.FILTER_TREE,
			filtered_column: 'ancestorcategoryid',
			options: {{ datagrid_filter.categoryid }},
			load_children: xajax_LoadCategoryChildren
		}
	});

	var column_adddate = new GF_Datagrid_Column({
		id: 'adddate',
		caption: '{% trans %}TXT_ADDDATE{% endtrans %}',
		appearance: {
			width: 140,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_startdate = new GF_Datagrid_Column({
		id: 'startdate',
		caption: '{% trans %}TXT_START_DATE{% endtrans %}',
		appearance: {
			width: 140,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_enddate = new GF_Datagrid_Column({
		id: 'enddate',
		caption: '{% trans %}TXT_END_DATE{% endtrans %}',
		appearance: {
			width: 140,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
    var options = {
		id: 'productpromotion',
		mechanics: {
			key: 'idproduct',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllProductPromotion,
			edit_row: editProductPromotion,
			delete_row: deleteProductPromotion,
			delete_group: deleteMultipleProductPromotion,
		},
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		],
		columns: [
			column_id,
			column_name,
			column_category,
			column_adddate,
			column_startdate,
			column_enddate
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-productnews'), options);
});
</script>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}