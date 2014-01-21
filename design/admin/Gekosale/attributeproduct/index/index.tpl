{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/atributes-list.png" alt=""/>{% trans %}TXT_ATTRIBUTE_PRODUCTS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_ATTRIBUTE_PRODUCT{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_ATTRIBUTE_PRODUCT{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="datagrid"></div>
</div>

<script type="text/javascript">

function editAttribute(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteAttributeProducts(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteAttributeProducts(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleAttributeProducts(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteAttributeProducts(p.dg, p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};
   
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idattributeproduct',
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
			width: 130
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetNameSuggestions,
		}
	});
	
	var column_value_count = new GF_Datagrid_Column({
		id: 'valuecount',
		caption: '{% trans %}TXT_VALUE_COUNT{% endtrans %}',
		appearance: {
			width: 130
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
	
	var column_product_count = new GF_Datagrid_Column({
		id: 'productcount',
		caption: '{% trans %}TXT_PRODUCT_COUNT{% endtrans %}',
		appearance: {
			width: 130
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
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
	
	var options = {
		id: 'attributeproduct',
		mechanics: {
			key: 'idattributeproduct',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllAttributeProducts,
			delete_row: deleteAttributeProducts,
			edit_row: editAttribute,
			delete_group: deleteMultipleAttributeProducts,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editAttribute
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_value_count,
			column_product_count,
			column_adddate,
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
    
    var theDatagrid = new GF_Datagrid($('#datagrid'), options);
		
});

</script>
{% endblock %}