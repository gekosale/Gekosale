{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-list.png" alt=""/>{% trans %}TXT_TRANSMAIL_FOOTER_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_TEMPLATE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_TEMPLATE{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-transmailfooters"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
    function editTransmailfooter(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
   
	 function deleteTransmailfooter(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteTransmailfooter(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleTransmailfooter(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteTransmailfooter(p.dg, p.ids);
		};
		new GF_Alert(title, msg, func, true, params);
	 };
	
	var theDatagrid;
	 
   $(document).ready(function() {

		var column_id = new GF_Datagrid_Column({
			id: 'idtransmailfooter',
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
			id: 'transmailfooter',
			mechanics: {
				key: 'idtransmailfooter',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllTransmailfooter,
				delete_row: deleteTransmailfooter,
				edit_row: editTransmailfooter,
				delete_group: deleteMultipleTransmailfooter,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editTransmailfooter
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_adddate
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-transmailfooters'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}