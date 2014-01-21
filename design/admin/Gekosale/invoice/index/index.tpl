{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/invoice-list.png" alt=""/>{% trans %}TXT_VIEW_ORDER_INVOICES{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="#" id="export" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/save.png" alt="" />{% trans %}TXT_EXPORT_SELECTED{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-invoice"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
    var theDatagrid;

    function viewOrder(dg, id) {
        location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + id + '';
	};

	function deleteOrder(dg, id) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteInvoice(p.id, p.dg);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
   $(document).ready(function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idinvoice',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_invoicedate = new GF_Datagrid_Column({
			id: 'invoicedate',
			caption: '{% trans %}TXT_DATE{% endtrans %}',
			appearance: {
				width: 140,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_symbol = new GF_Datagrid_Column({
			id: 'symbol',
			caption: '{% trans %}TXT_INVOICE{% endtrans %}',
			appearance: {
				width: GF_Datagrid.WIDTH_AUTO,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_orderid = new GF_Datagrid_Column({
			id: 'orderid',
			caption: '{% trans %}TXT_ORDER{% endtrans %}',
			appearance: {
				width: 140
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
    	var options = {
			id: 'invoice',
			mechanics: {
				key: 'idinvoice'
			},
			event_handlers: {
				load: xajax_LoadAllInvoice,
				view_row: viewOrder,
				delete_row: deleteOrder,
			},
			columns: [
				column_id,
				column_invoicedate,
				column_symbol,
				column_orderid,
			],
			row_actions: [
				GF_Datagrid.ACTION_DELETE,
				GF_Datagrid.ACTION_VIEW,
			],
    };
    
    theDatagrid = new GF_Datagrid($('#list-invoice'), options);

    $('#export').click(function(){
    	var selected = theDatagrid.GetSelected();
    	if(selected.length){
			location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/confirm/'+ Base64.encode(JSON.stringify(selected));
    	}else{
    		var title = '{% trans %}TXT_INVOICE_EXPORT{% endtrans %}';
			var msg = '{% trans %}ERR_EMPTY_INVOICES_SELECTED_LIST{% endtrans %}';
			var params = {};
			var func = function(p) {

			};
	    	new GMessage(title, msg);
    	}
		return false;
    });
    
	 });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}