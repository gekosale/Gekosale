{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/view.png" alt=""/>Rejestr kuponów</h2>

<div class="block">
	<div id="list-points-rewards"></div>
</div>

<script type="text/javascript">
   
	function editCouponsRegistry(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
	 
	var theDatagrid;
	 
   $(document).ready(function() {
		

		var column_id = new GF_Datagrid_Column({
			id: 'id',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
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
				width: 140
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_surname = new GF_Datagrid_Column({
			id: 'surname',
			caption: '{% trans %}TXT_SURNAME{% endtrans %}',
			appearance: {
				width: 140
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_email = new GF_Datagrid_Column({
			id: 'email',
			caption: '{% trans %}TXT_EMAIL{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_coupon = new GF_Datagrid_Column({
			id: 'coupon',
			caption: 'Kupon',
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.coupon }}
				],
			}
		});
		
		var column_order = new GF_Datagrid_Column({
			id: 'orderid',
			caption: '{% trans %}TXT_ORDER_DATA{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_globalprice = new GF_Datagrid_Column({
			id: 'globalprice',
			caption: '{% trans %}TXT_TOTAL_ORDER_VALUE{% endtrans %}',
			appearance: {
				width: 140
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		
		var column_view = new GF_Datagrid_Column({
			id: 'view',
			caption: '{% trans %}TXT_STORES{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.view }}
				],
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
			id: 'couponsregistry',
			mechanics: {
				key: 'id'
			},
			event_handlers: {
				load: xajax_LoadAllCouponsRegistry,
				edit_row: editCouponsRegistry
			},
			columns: [
				column_id,
				column_surname,
				column_firstname,
				column_email,
				column_coupon,
				column_order,
				column_globalprice,
				column_adddate
			],
			row_actions: [
				
			],
			context_actions: [
				
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-points-rewards'), options);
		
	 });
   
   /*]]>*/
   
  </script>
{% endblock %}