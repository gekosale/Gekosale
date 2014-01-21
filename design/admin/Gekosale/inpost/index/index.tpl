{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/order-list.png" alt=""/>Zamówienia do wysłania poprzez Inpost</h2>

<ul class="possibilities">
	<li><a href="#" id="export" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/save.png" alt="" />{% trans %}TXT_EXPORT_SELECTED{% endtrans %}</span></a></li>
	<li><a href="{{ URL }}inpost/confirm" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/datagrid/update.png" alt=""/>Aktualizuj statusy paczek</span></a></li>
</ul>

<div class="block">
	<div id="list-orders"></div>
</div>

<script type="text/javascript">



   /*<![CDATA[*/

     function createPackage(dg, id) {
    	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/add/' + id + '';
   		};

	 function downloadSlip(dg, id) {
		var oRow = theDatagrid.GetRow(id);
    	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + oRow.inpostpackage + '';
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

		var column_paczkomat = new GF_Datagrid_Column({
			id: 'paczkomat',
			caption: 'Paczkomat',
			appearance: {
				width: 90,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_inpostpackage = new GF_Datagrid_Column({
			id: 'inpostpackage',
			caption: 'Numer paczki',
			appearance: {
				width: 90,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_packagestatus = new GF_Datagrid_Column({
			id: 'packagestatus',
			caption: 'Status paczki',
			appearance: {
				width: 90,
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{id: '', caption: ''},
					{id: 'Pending', caption: 'Oczekuje na przygotowanie'},
					{id: 'Created', caption: 'Oczekuje na wysyłkę'},
					{id: 'Prepared', caption: 'Gotowa do wysyłki'},
					{id: 'Sent', caption: 'Przesyłka Nadana'},
					{id: 'InTransit', caption: 'W drodze'},
					{id: 'Stored', caption: 'Oczekuje na odbiór'},
					{id: 'Avizo', caption: 'Ponowne Avizo'},
					{id: 'Expired', caption: 'Nie odebrana'},
					{id: 'Delivered', caption: 'Dostarczona'},
					{id: 'RetunedToAgency', caption: 'Przekazana do Oddziału'},
					{id: 'Cancelled', caption: 'Anulowana'},
					{id: 'Claimed', caption: 'Przyjęto zgłoszenie reklamacyjne'},
					{id: 'ClaimProcessed', caption: 'Rozpatrzono zgłoszenie reklamacyjne'},
				],
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

		var action_createPackage = new GF_Action({
			img: '{{ DESIGNPATH }}/_images_panel/icons/datagrid/add.png',
			caption: 'Wygeneruj paczkę',
			action: createPackage,
			condition: function(oR) { return oR['inpostpackage'] == ''; }
		});

		var action_downloadSlip = new GF_Action({
			img: '{{ DESIGNPATH }}/_images_panel/icons/datagrid/save.png',
			caption: 'Pobierz etykietę',
			action: downloadSlip,
			condition: function(oR) { return oR['inpostpackage'] != ''; }
		});

	    var options = {
			id: 'order',
			mechanics: {
				key: 'idorder',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllOrder,
			},
			columns: [
				column_id,
				column_client,
				column_paczkomat,
				column_inpostpackage,
				column_packagestatus,
				column_price,
				column_globalprice,
				column_adddate,
			],
			row_actions: [
				action_createPackage,
				action_downloadSlip
			],
			context_actions: [
				action_createPackage,
				action_downloadSlip
			],
			group_actions: [
			],

    };

    theDatagrid = new GF_Datagrid($('#list-orders'), options);

    $('#export').click(function(){
    	var selected = theDatagrid.GetSelected();
    	if(selected.length){
			location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/'+ Base64.encode(JSON.stringify(selected));
    	}else{
    		var title = '{% trans %}TXT_INPOST_EXPORT{% endtrans %}';
			var msg = '{% trans %}ERR_EMPTY_INPOST_SELECTED_LIST{% endtrans %}';
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