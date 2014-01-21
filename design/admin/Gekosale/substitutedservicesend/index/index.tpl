{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-list.png" alt=""/>{% trans %}TXT_SUBSTITUTED_SERVICE{% endtrans %}</h2>
<div class="block">
	<div id="list-substitutedservices"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
   function sendSubstitutedService(dg, id) {
	   location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/confirm/' + id + '';
   };
   
   function viewSubstitutedService(dg, id) {
	   location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + id + '';
   };
 
   var theDatagrid;
	 
   $(document).ready(function() {
	   
	   var action_sendSubstitutedService = new GF_Action({
			caption: '{% trans %}TXT_SEND{% endtrans %}',
			action: sendSubstitutedService,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/send.png',
			condition: function(oR) { return oR['disable'] != '0'; }
		 });
	   
	   var action_viewSubstitutedService = new GF_Action({
			caption: '{% trans %}TXT_VIEW_REPORT{% endtrans %}',
			action: viewSubstitutedService,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/report.png',
			condition: function(oR) { return oR['disable'] != '0'; }
		 });

		var column_id = new GF_Datagrid_Column({
			id: 'idsubstitutedservice',
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
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_transmailname = new GF_Datagrid_Column({
			id: 'transmailname',
			caption: '{% trans %}TXT_TRANSMAIL{% endtrans %}',
			appearance: {
				width: 250,
			}
		});
		
    var options = {
			id: 'idsubstitutedservice',
			mechanics: {
				key: 'idsubstitutedservice'
			},
			event_handlers: {
				load: xajax_LoadAllSubstitutedservice
			},
			columns: [
				column_id,
				column_name,
				column_transmailname
			],
			row_actions: [
			  action_sendSubstitutedService,
			  action_viewSubstitutedService
			],
			context_actions: [
			  action_sendSubstitutedService,
			  action_viewSubstitutedService
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-substitutedservices'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}