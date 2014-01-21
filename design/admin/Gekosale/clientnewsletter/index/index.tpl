{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/clientnewsletter-list.png" alt=""/>{% trans %}TXT_CLIENT_NEWSLETTERS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ CURRENT_CONTROLLER }}/view" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/save.png" alt="" />{% trans %}TXT_EXPORT{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-clientnewsletters"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
	 function deleteClientNewsletter(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.email +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteClientNewsletter(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleClientNewsletter(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteClientNewsletter(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
   
   	function enableClientNewsletter(dg, id) {
		xajax_enableClientNewsletter(dg, id);
	 };
	 
	 function disableClientNewsletter(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DISABLE{% endtrans %}';
		var msg = '{% trans %}TXT_DISABLE_CONFIRM{% endtrans %} <strong>' + oRow.email +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_disableClientNewsletter(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };	
	 
	 function enableClientNewsletter(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_ENABLE{% endtrans %}';
		var msg = '{% trans %}TXT_ENABLE_CONFIRM{% endtrans %} <strong>' + oRow.email +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_enableClientNewsletter(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
	 
   $(document).ready(function() {
   
  		 var action_enableClientNewsletter = new GF_Action({
			caption: '{% trans %}TXT_ENABLE_CLIENT{% endtrans %}',
			action: enableClientNewsletter,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/deactivate.png',
			condition: function(oR) { return oR['active'] != '{% trans %}TXT_ACTIVE{% endtrans %}'; }
		 });
		 
		 var action_disableClientNewsletter = new GF_Action({
			caption: '{% trans %}TXT_DISABLE_CLIENT{% endtrans %}',
			action: disableClientNewsletter,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/activate.png',
			condition: function(oR) { return oR['active'] == '{% trans %}TXT_ACTIVE{% endtrans %}'; }
		 });
		
		var column_id = new GF_Datagrid_Column({
			id: 'idclientnewsletter',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_email = new GF_Datagrid_Column({
			id: 'email',
			caption: '{% trans %}TXT_EMAIL{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_adddate = new GF_Datagrid_Column({
			id: 'adddate',
			caption: '{% trans %}TXT_ADDDATE{% endtrans %}',
			appearance: {
				width: 140,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

    	var options = {
			id: 'clientnewsletter',
			mechanics: {
				key: 'idclientnewsletter',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllClientNewsletter,
				delete_row: deleteClientNewsletter,
				delete_group: deleteMultipleClientNewsletter,
			},
			columns: [
				column_id,
				column_email,
				column_adddate
			],
			row_actions: [
				action_enableClientNewsletter,
				action_disableClientNewsletter,
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
  				action_enableClientNewsletter,
				action_disableClientNewsletter,
				GF_Datagrid.ACTION_DELETE
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-clientnewsletters'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}