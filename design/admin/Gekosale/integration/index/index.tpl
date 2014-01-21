{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/rulescart-list.png" alt=""/>{% trans %}TXT_INTEGRATION_LIST{% endtrans %}</h2>
<div class="block">
	<div id="list-integration"></div>
</div>
<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
   	 function editIntegration(dg, id) {
   		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
	 
	 var theDatagrid;
	  
   $(document).ready(function() {
   
		var column_id = new GF_Datagrid_Column({
			id: 'idintegration',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: '{% trans %}TXT_INTEGRATION_NAME{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_symbol = new GF_Datagrid_Column({
			id: 'symbol',
			caption: '{% trans %}TXT_INTEGRATION_SYMBOL{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		
		
		

    var options = {
			id: 'integration',
			mechanics: {
				key: 'idintegration',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllIntegration,
				edit_row: editIntegration,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editIntegration
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_symbol
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT
			],
			group_actions: [
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-integration'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}