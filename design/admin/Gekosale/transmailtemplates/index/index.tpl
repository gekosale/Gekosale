{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-list.png" alt=""/>{% trans %}TXT_TRANSMAILS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_TEMPLATE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_TEMPLATE{% endtrans %}</span></a></li>
	<li><a href="#" id="refresh" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/datagrid/refresh.png" alt=""/>{% trans %}TXT_REFRESH_TRANSMAIL{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-transmails"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
    function editTransmail(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
   
	 function deleteTransmail(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteTransmail(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleTransmail(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteTransmail(p.dg, p.ids);
		};
		new GF_Alert(title, msg, func, true, params);
	 };
	 
	 
	 function setDefaultTransMailTemplate(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DEFAULT{% endtrans %}';
		var msg = '{% trans %}TXT_SET_DEFAULT{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_setDefaultTransMailTemplate(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	};	
	
	var theDatagrid;
	 
   $(document).ready(function() {
	   
	   var action_setDefaultTransMailTemplate = new GF_Action({
		   caption: '{% trans %}TXT_SET_DEFAULT{% endtrans %}',
		   action: setDefaultTransMailTemplate,
	   		img:'{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
	   		condition: function(oR) { return oR['active'] == '0'; }
		   
	   });
		
		var column_id = new GF_Datagrid_Column({
			id: 'idtransmail',
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
		
		var column_active = new GF_Datagrid_Column({
			id: 'active',
			caption: '{% trans %}TXT_DEFAULT{% endtrans %}',
			appearance: {
				width: 20,
				visible: true
			}
		});
		
		
		var column_action = new GF_Datagrid_Column({
			id: 'action',
			caption: '{% trans %}TXT_ACTION{% endtrans %}'
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
		
    var options = {
			id: 'transmail',
			mechanics: {
				key: 'idtransmail',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllTransmail,
				delete_row: deleteTransmail,
				edit_row: editTransmail,
				delete_group: deleteMultipleTransmail,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editTransmail
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_active,
				column_action,
				column_adddate
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE,
				action_setDefaultTransMailTemplate
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE,
				action_setDefaultTransMailTemplate
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-transmails'), options);
    
   });

   $(document).ready(function() {
		$('#refresh').click(function(){
			var title = '{% trans %}TXT_REFRESH_TRANSMAIL{% endtrans %}';
			var msg = '{% trans %}TXT_REFRESH_TRANSMAIL_HELP{% endtrans %}?';
			var params = {};
			var func = function(p) {
				return xajax_doRefreshTransmail();
			};
			new GF_Alert(title, msg, func, true, params);
		});
	});
   /*]]>*/
   
   
   
  </script>
{% endblock %}