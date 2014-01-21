{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/mostsearch-list.png" alt=""/>{% trans %}TXT_SPY{% endtrans %}</h2>

<div class="block">
	<div id="list-spy"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
    var theDatagrid;
    
   $(document).ready(function() {
   
   		function editClient(dg, id) {
    		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 	};
   
  		var column_id = new GF_Datagrid_Column({
			id: 'id',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
		});
		
		var column_client = new GF_Datagrid_Column({
			id: 'client',
			caption: '{% trans %}TXT_CLIENT{% endtrans %}',
		});
		
		var column_session = new GF_Datagrid_Column({
			id: 'client_session',
			caption: '{% trans %}TXT_SESSION{% endtrans %}',
			appearance: {
				visible: false
			},
		});
		
		var column_lastaddress = new GF_Datagrid_Column({
			id: 'lastaddress',
			caption: '{% trans %}TXT_SPY_LAST_ADDRESS{% endtrans %}',
		});
		
		var column_cart = new GF_Datagrid_Column({
			id: 'cart',
			caption: '{% trans %}TXT_CART{% endtrans %}',
			appearance: {
				width: 110,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_ipaddress = new GF_Datagrid_Column({
			id: 'ipaddress',
			caption: 'IP',
			appearance: {
				width: 110,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_isbot = new GF_Datagrid_Column({
			id: 'isbot',
			caption: '{% trans %}TXT_SPY_ISBOT{% endtrans %}',
			appearance: {
				width: 70,
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.isbot }}
				],
			}
		});
		var column_ismobile = new GF_Datagrid_Column({
			id: 'ismobile',
			caption: '{% trans %}TXT_SPY_ISMOBILE{% endtrans %}',
			appearance: {
				width: 70,
			},
			filter: {
			type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.ismobile }}
				],
			}
		});
		var column_browser = new GF_Datagrid_Column({
			id: 'browser',
			caption: '{% trans %}TXT_SPY_BROWSER{% endtrans %}',
			appearance: {
				width: 100,
			},
			filter: {
			type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.browser }}
				],
			}
		});
		var column_platform = new GF_Datagrid_Column({
			id: 'platform',
			caption: '{% trans %}TXT_SPY_PLATFORM{% endtrans %}',
			appearance: {
				width: 90,
			},
			filter: {
			type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.platform }}
				],
			}
		});
		
    var options = {
			id: 'spy',
			mechanics: {
				key: 'client_session',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllSpy,
				edit_row: editClient,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editClient
				{% endif %}
			},
			columns: [
				column_id,
				column_client,
				column_lastaddress,
				column_ipaddress,
				column_session,
				column_cart,
				column_isbot,
				column_ismobile,
				column_browser,
				column_platform,
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT
			]
			
			
    };
    
    theDatagrid = new GF_Datagrid($('#list-spy'), options);
    
   });
   
   /*]]>*/
   
   
   
</script>
{% endblock %}



{% block sticky %}
{% include sticky %}
{% endblock %}