{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/product-list.png" alt=""/>{% trans %}TXT_PRODUCTS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_PRODUCT{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_PRODUCT{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-products"></div>
</div>
<script type="text/javascript">
/*<![CDATA[*/

function processProduct(row) {

	if (row.thumb != '') {
		row.name = '<a title="" href="' + row.thumb + '" class="show-thumb"><img src="{{ DESIGNPATH }}_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" /></a> '+ row.name;
	}else{
		row.name = '<img style="opacity: 0.2;vertical-align: middle;" src="{{ DESIGNPATH }}_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" /> '+ row.name;
	}
	return {
		idproduct: row.idproduct,
		hierarchy: row.hierarchy,
		trackstock: row.trackstock,
		disableatstockenabled: row.disableatstockenabled,
		attributes: row.attributes,
		name: row.name,
		seo: row.seo,
		thumb: row.thumb,
		name: row.name,
		delivelercode: row.delivelercode,
		ean: row.ean,
		producer: row.producer,
		deliverer: row.deliverer,
		buyprice: row.buyprice,
		buyprice_gross: row.buyprice_gross,
		stock: row.stock,
		sellprice: row.sellprice,
		sellprice_gross: row.sellprice_gross,
		weight: row.weight,
		status: row.status,
		adddate: row.adddate,
		enable: row.enable,
		categoriesname: row.categoriesname
	};
};

function dataLoaded(dDg) {
	dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
};
 
function editProduct(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function editProductTab(dg, id) {
	window.open('{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id);
};

function viewProduct(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	window.open(oRow.seo);
};

function duplicateProduct(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/duplicate/' + id + '';
};

function deleteProduct(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + oRow.name + '?';
	var params = {
		dg: dg,
		id: id,
		view: {{ view }}
	};
	var func = function(p) {
		return xajax_doDeleteProduct(p.id, p.dg);
	};
	new GF_Alert(title, msg, func, true, params);
};

function deleteMultipleProducts(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids,
		view: {{ view }}
	};
	var func = function(p) {
		return xajax_doDeleteProduct(p.ids, p.dg, p.view);
	};
	new GF_Alert(title, msg, func, true, params);
};

function disableProduct(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_PUBLISH{% endtrans %}';
	var msg = '{% trans %}TXT_DISABLE_PRODUCT{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_setProductEnable(p.dg, p.id,0);
	};
	new GF_Alert(title, msg, func, true, params);
 };	
 
 function enableProduct(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_PUBLISH{% endtrans %}';
	var msg = '{% trans %}TXT_ENABLE_PRODUCT{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_setProductEnable(p.dg, p.id,1);
	};
	new GF_Alert(title, msg, func, true, params);
 };
 
var theDatagrid;

$(document).ready(function() {

	var column_id = new GF_Datagrid_Column({
		id: 'idproduct',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		sorting: {
			default_order: GF_Datagrid.SORT_DIR_DESC
		},
		appearance: {
			width: 40,
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
			width: 160,
			align: GF_Datagrid.ALIGN_LEFT
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetNameSuggestions,
		}
	});

	var column_delivelercode = new GF_Datagrid_Column({
		id: 'delivelercode',
		caption: '{% trans %}TXT_DELIVELERCODE{% endtrans %}',
		appearance: {
			width: 80,
			align: GF_Datagrid.ALIGN_RIGHT,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
	
	var column_ean = new GF_Datagrid_Column({
		id: 'ean',
		caption: '{% trans %}TXT_EAN{% endtrans %}',
		editable: true,
		appearance: {
			width: 60,
			align: GF_Datagrid.ALIGN_RIGHT
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
	
	var column_hierarchy = new GF_Datagrid_Column({
		id: 'hierarchy',
		caption: '{% trans %}TXT_HIERARCHY{% endtrans %}',
		editable: true,
		appearance: {
			width: 40,
			align: GF_Datagrid.ALIGN_RIGHT
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_weight = new GF_Datagrid_Column({
		id: 'weight',
		caption: '{% trans %}TXT_WEIGHT{% endtrans %}',
		appearance: {
			width: 40,
			align: GF_Datagrid.ALIGN_RIGHT,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_price_gross = new GF_Datagrid_Column({
		id: 'sellprice_gross',
		caption: '{% trans %}TXT_JS_PRODUCT_SELECT_SUBSUM{% endtrans %}',
		editable: true,
		appearance: {
			width: 40,
			align: GF_Datagrid.ALIGN_RIGHT
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_buyprice_gross = new GF_Datagrid_Column({
		id: 'buyprice_gross',
		caption: '{% trans %}TXT_BUYPRICE_GROSS{% endtrans %}',
		appearance: {
			width: 60,
			visible: false,
			align: GF_Datagrid.ALIGN_RIGHT
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_stock = new GF_Datagrid_Column({
		id: 'stock',
		caption: '{% trans %}TXT_JS_PRODUCT_VARIANTS_EDITOR_STOCK{% endtrans %}',
		editable: true,
		appearance: {
			width: 40,
			align: GF_Datagrid.ALIGN_RIGHT
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_status = new GF_Datagrid_Column({
		id: 'status',
		caption: '{% trans %}TXT_STATUS{% endtrans %}',
		appearance: {
			width: 60
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: {{ status_filter|json_encode() }}
		}
	});
	
	var column_enable = new GF_Datagrid_Column({
		id: 'enable',
		caption: '{% trans %}TXT_PUBLISH{% endtrans %}',
		selectable: true,
		appearance: {
			width: 40
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{id: '', caption: ''}, {id: '1', caption: '{% trans %}TXT_YES{% endtrans %}'}, {id: '0', caption: '{% trans %}TXT_NO{% endtrans %}'}
			],
		}
	});
	
	var column_producer = new GF_Datagrid_Column({
		id: 'producer',
		caption: '{% trans %}TXT_PRODUCER{% endtrans %}',
		selectable: true,
		appearance: {
			width: 70
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{{ datagrid_filter.producer }}
			],
		}
	});

	var column_deliverer = new GF_Datagrid_Column({
		id: 'deliverer',
		caption: '{% trans %}TXT_DELIVERER{% endtrans %}',
		appearance: {
			width: 60,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [{{ datagrid_filter.deliverer }}],
		}
	});

	var column_category = new GF_Datagrid_Column({
		id: 'categoriesname',
		caption: '{% trans %}TXT_CATEGORY{% endtrans %}',
		appearance: {
			width: 120
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
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var action_enableProduct = new GF_Action({
		caption: '{% trans %}TXT_PUBLISH{% endtrans %}',
		action: enableProduct,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/off.png',
		condition: function(oR) { return oR['enable'] == '0'; }
	});
	 
	var action_disableProduct= new GF_Action({
		caption: '{% trans %}TXT_NOT_PUBLISH{% endtrans %}',
		action: disableProduct,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
		condition: function(oR) { return oR['enable'] == '1'; }
	});
	
	var action_duplicate = new GF_Action({
		img: '{{ DESIGNPATH }}/_images_panel/datagrid/duplicate-row.png',
		caption: '{% trans %}TXT_JS_TREE_DUPLICATE_ITEM{% endtrans %}',
		action: duplicateProduct
	});
	
	var action_edittab = new GF_Action({
		img: '{{ DESIGNPATH }}/_images_panel/icons/datagrid/edit.png',
		caption: '{% trans %}TXT_EDIT_NEW_TAB{% endtrans %}',
		action: editProductTab
	});

	var options = {
		id: 'product',
		mechanics: {
			key: 'idproduct',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllProduct,
			process: processProduct,
			delete_row: deleteProduct,
			loaded: dataLoaded,
			edit_row: editProduct,
			view_row: viewProduct,
			delete_group: deleteMultipleProducts,
			update_row: function(sId, oRow, sColumn, sPreviousValue) {
				if(sColumn == 'stock' && parseInt(oRow.attributes) > 0){
					oRow.stock = sPreviousValue;
					theDatagrid.Rewrite();
					return GError('Niemożliwa aktualizacja stanu magazynowego.', 'Ten produkt zawiera warianty. Stan magazynowy obliczany jest automatycznie jako suma stanów wszystkich wariantów produktu.');
				}else if(sColumn == 'enable' && parseInt(oRow.disableatstockenabled) == 1){
					theDatagrid.Rewrite();
					return GError('Niemożliwa aktualizacja widoczności produktu.', 'Ten produkt ma włączone śledzenie stanu magazynowego. Jego widoczność określana jest automatycznie na podstawie stanu magazynowego.');
				}else{
					xajax_doUpdateProduct({
						id: sId,
						product: oRow
					}, GCallback(function(eEvent) {
						theDatagrid.LoadData();
					}));
				}
			},
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editProduct
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_category,
			column_delivelercode,
			column_ean,
			column_hierarchy,
			column_producer,
			column_deliverer,
			column_buyprice_gross,
			column_price_gross,
			column_stock,
			column_weight,
			column_status,
			column_enable,
			column_adddate,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			//action_enableProduct,
			//action_disableProduct,
			action_duplicate,
			GF_Datagrid.ACTION_VIEW,
			GF_Datagrid.ACTION_DELETE
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
			GF_Datagrid.ACTION_VIEW,
			action_duplicate,
			action_edittab,
		]
	};

	theDatagrid = new GF_Datagrid($('#list-products'), options);

});

/*]]>*/



  </script>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}