{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/order-list.png" alt=""/>Sendit.pl</h2>

<ul class="possibilities">
    <li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" id="add_order" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_ORDERS{% endtrans %}</span></a></li>
    <li><a href="{{ URL }}exchange/view/4" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/xls.png" alt=""/>{% trans %}TXT_EXPORT_ORDERS{% endtrans %}</span></a></li>
</ul>

<div class="block">
    <div id="list-orders"></div>
</div>

{% if view == 0 %}
<script type="text/javascript">
	$(document).ready(function(){
		$('#add_order').click(function(){
			var title = '{% trans %}TXT_SELECT_VIEW_FROM_ORDER{% endtrans %}';
			var msg = '';
			var params = {};
			new GF_Alert(title, msg, true, true, params);
			return false;
		});
	});
</script>
{% endif %}

<script type="text/javascript">
	function viewOrder(dg, id) {
		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + id + '';
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

		var column_price = new GF_Datagrid_Column({
			id: 'price',
			caption: '{% trans %}TXT_ORDER_BASE_VALUE{% endtrans %}',
			appearance: {
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
				options: [{{ datagrid_filter.dispatchmethodname }}],
			}
		});

		var column_paymentmethodname = new GF_Datagrid_Column({
			id: 'paymentmethodname',
			caption: '{% trans %}TXT_PAYMENT_METHOD{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [{{ datagrid_filter.paymentmethodname }}],
			}
			});

		var column_clientid = new GF_Datagrid_Column({
			id: 'clientid',
			caption: '{% trans %}TXT_CLIENT{% endtrans %}',
			appearance: {
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [{{ datagrid_filter.clientid }}],
			}
		});

		var column_adddate = new GF_Datagrid_Column({
			id: 'adddate',
			caption: '{% trans %}TXT_ADDDATE{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var options = {
			id: 'order',
			mechanics: {
				key: 'idorder',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllOrder,
				view_row: viewOrder,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: viewOrder
				{% endif %}
			},
			columns: [
				column_id,
				column_client,
				column_products,
				column_price,
				column_dispatchmethodprice,
				column_orderstatusname,
				column_dispatchmethodname,
				column_paymentmethodname,
				column_clientid,
				column_globalprice,
				column_adddate,
			],
			row_actions: [
				// GF_Datagrid.ACTION_EDIT,
			],
			context_actions: [
				GF_Datagrid.ACTION_VIEW,
			],
		};

		theDatagrid = new GF_Datagrid($('#list-orders'), options);

		$('.GF_Datagrid_Col_adddate .GF_Datagrid_filter_between .from,.GF_Datagrid_Col_adddate .GF_Datagrid_filter_between .to').datepicker({dateFormat: 'yy-mm-dd'});
});
</script>

{% endblock %}