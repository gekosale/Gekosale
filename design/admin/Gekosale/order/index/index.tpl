{% extends "layout.tpl" %} {% block content %}
<h2>
	<img src="{{ DESIGNPATH }}_images_panel/icons/modules/order-list.png"
		alt="" />{% trans %}TXT_ORDERS_LIST{% endtrans %}
</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"
		id="add_order" title="{% trans %}TXT_ADD_ORDERS{% endtrans %}"><span><img
				src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt="" />{%
				trans %}TXT_ADD_ORDERS{% endtrans %}</span></a></li>
</ul>

{% if view == 0 %}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_order').click(function(){
		var title = '{% trans %}TXT_SELECT_VIEW_FROM_ORDER{% endtrans %}';
		var msg = '';
   	 	new GMessage(title, msg);
      	return false;
	});
});
</script>
{% endif %}

<div class="block">
	<div id="list-orders"></div>
</div>

<script type="text/javascript">

function processOrder(row) {

	return {
		idorder: row.idorder,
		client: row.client,
		delivery: row.delivery,
		price: row.price + ' ' + row.currencysymbol,
		products: row.products,
		globalprice: row.globalprice + ' ' + row.currencysymbol,
		dispatchmethodprice: row.dispatchmethodprice + ' ' + row.currencysymbol,
		orderstatusname: '<span style="color: #' + row.colour + ';">' + row.orderstatusname +'</span>',
		dispatchmethodname: row.dispatchmethodname,
		paymentmethodname: row.paymentmethodname,
		isallegro: (row.isallegro == 1) ? '{% trans %}TXT_YES{% endtrans %}':'{% trans %}TXT_NO{% endtrans %}',
		clientid: row.clientid,
		comments: row.comments,
		adddate: row.adddate,
	};
};

function dataLoaded(dDg) {
	dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowInfoForThis).mouseleave(GTooltip.HideInfoForThis);
};
 
function viewOrder(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + id + '';
};
	 
function editOrder(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteOrder(dg, id) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteOrder(p.id, p.dg);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleOrders(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteOrder(p.ids, p.dg);
	};
    new GF_Alert(title, msg, func, true, params);
};

function changeStatus(dg, id, status) {
	return xajax_doChangeOrderStatus(id, dg, status);
};

function changeStatusMulti(dg, ids, status) {
	return xajax_doChangeOrderStatus(ids, dg, status);
};

var theDatagrid;
   
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idorder',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 30
		},
		sorting: {
			default_order: GF_Datagrid.SORT_DIR_DESC
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
		var column_client = new GF_Datagrid_Column({
			id: 'client',
			caption: '{% trans %}TXT_CLIENT{% endtrans %}',
			appearance: {
				width: 140
			},
			filter: {
				type: GF_Datagrid.FILTER_AUTOSUGGEST,
				source: xajax_GetClientSuggestions,
			}
		});
		
		var column_delivery = new GF_Datagrid_Column({
			id: 'delivery',
			caption: '{% trans %}TXT_DELIVER{% endtrans %}',
			appearance: {
				width: 140,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'price',
			caption: '{% trans %}TXT_ORDER_BASE_VALUE{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_products = new GF_Datagrid_Column({
			id: 'products',
			caption: '{% trans %}TXT_PRODUCTS{% endtrans %}',
			appearance: {
				width: 190,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_globalprice = new GF_Datagrid_Column({
			id: 'globalprice',
			caption: '{% trans %}TXT_TOTAL_ORDER_VALUE{% endtrans %}',
			appearance: {
				width: 80
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_dispatchmethodprice = new GF_Datagrid_Column({
			id: 'dispatchmethodprice',
			caption: '{% trans %}TXT_DISPATCHMETHODPRICE{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_orderstatusname = new GF_Datagrid_Column({
			id: 'orderstatusname',
			caption: '{% trans %}TXT_CURRENT_ORDER_STATUS{% endtrans %}',
			appearance: {
				width: 190,
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'orderstatusid',
				options: {{ datagrid_filter.orderstatusid }},
			}
		});
		
		var column_dispatchmethodname = new GF_Datagrid_Column({
			id: 'dispatchmethodname',
			caption: '{% trans %}TXT_DISPATCH_METHOD{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.dispatchmethodname }}
				],
			}
		});
		
		var column_paymentmethodname = new GF_Datagrid_Column({
			id: 'paymentmethodname',
			caption: '{% trans %}TXT_PAYMENT_METHOD{% endtrans %}',
			appearance: {
				width: 90,
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.paymentmethodname }}
				],
			}
		});
		
		var column_adddate = new GF_Datagrid_Column({
			id: 'adddate',
			caption: '{% trans %}TXT_ADDDATE{% endtrans %}',
			appearance: {
				width: 110
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_comments = new GF_Datagrid_Column({
			id: 'comments',
			caption: '{% trans %}TXT_COMMENT{% endtrans %}',
			appearance: {
				width: 110,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_isallegro = new GF_Datagrid_Column({
			id: 'isallegro',
			caption: 'Allegro',
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
		
		var action_changeStatus = new GF_Action({
			img: '{{ DESIGNPATH }}/_images_panel/datagrid/change-status.png',
			caption: '{% trans %}TXT_CHANGE_STATUS{% endtrans %}',
			action: changeStatus,
			values: {{ order_statuses }}
		});
	
		var action_changeStatusMulti = new GF_Action({
			img: '{{ DESIGNPATH }}/_images_panel/datagrid/change-status.png',
			caption: '{% trans %}TXT_CHANGE_STATUS{% endtrans %}',
			action: changeStatusMulti,
			values: {{ order_statuses }}
		});
	
    	var options = {
			id: 'order',
			mechanics: {
				key: 'idorder',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllOrder,
				process: processOrder,
				loaded: dataLoaded,
				view_row: viewOrder,
				delete_row: deleteOrder,
				edit_row: editOrder,
				delete_group: deleteMultipleOrders,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editOrder
				{% endif %}
			},
			columns: [
				column_id,
				column_client,
				column_delivery,
				column_products,
				column_price,
				column_dispatchmethodprice,
				column_orderstatusname,
				column_dispatchmethodname,
				column_paymentmethodname,
				column_globalprice,
				column_adddate,
				column_comments,
				column_isallegro
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				action_changeStatus,
				GF_Datagrid.ACTION_DELETE,
			],
			context_actions: [
				action_changeStatus,
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				action_changeStatus,
				GF_Datagrid.ACTION_DELETE
			],
		
    };
    
    theDatagrid = new GF_Datagrid($('#list-orders'), options);
    
    $('.GF_Datagrid_Col_adddate .GF_Datagrid_filter_between .from').datepicker({dateFormat: 'yy-mm-dd 00:00:00'});
    $('.GF_Datagrid_Col_adddate .GF_Datagrid_filter_between .to').datepicker({dateFormat: 'yy-mm-dd 23:59:59'});

	});
   
   /*]]>*/
   
   
   
  </script>
{% endblock %} {% block sticky %} {% include sticky %} {% endblock %}
