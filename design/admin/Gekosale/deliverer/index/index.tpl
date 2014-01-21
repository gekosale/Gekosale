{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/deliverer-list.png" alt=""/>{% trans %}TXT_DELIVERER_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_DELIVERER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_DELIVERER{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-deliverer"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
	 
	 function editDeliverer(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };

	 function deleteDeliverer(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteDeliverer(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleDeliverers(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteDeliverer(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
	 
   $(document).ready(function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'iddeliverer',
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
				type: GF_Datagrid.FILTER_AUTOSUGGEST,
				source: xajax_GetNameSuggestions,
			}
		});
		
		var column_www = new GF_Datagrid_Column({
			id: 'www',
			caption: '{% trans %}TXT_WEBSITE{% endtrans %}',
			appearance: {
				width: 140
			},
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
			id: 'deliverer',
			mechanics: {
				key: 'iddeliverer',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllDeliverer,
				delete_row: deleteDeliverer,
				edit_row: editDeliverer,
				delete_group: deleteMultipleDeliverers,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editDeliverer
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_www,
				column_adddate,
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
    
    theDatagrid = new GF_Datagrid($('#list-deliverer'), options);
		
	 });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}
