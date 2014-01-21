{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/opinion-list.png" alt=""/>{% trans %}TXT_RANGETYPES_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_RANGETYPE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_RANGETYPE{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-rangetype"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
   function editRangeType(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
	 
	function deleteRangeType(dg, id) {
		var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteRangeType(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleRangeTypes(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteRangeType(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
	 
   $(document).ready(function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idrangetype',
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
			caption: '{% trans %}TXT_RANGETYPE{% endtrans %}',
			appearance: {
				width: 150
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_category = new GF_Datagrid_Column({
			id: 'categoriesname',
			caption: '{% trans %}TXT_CATEGORY{% endtrans %}',
			appearance: {
				width: 150
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'ancestorcategoryid',
				options: {{ datagrid_filter.categoryid }},
				load_children: xajax_LoadCategoryChildren
			}
		});
		
    var options = {
			id: 'rangetype',
			mechanics: {
				key: 'idrangetype',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllRangeType,
				edit_row: editRangeType,
				delete_row: deleteRangeType,
				delete_group: deleteMultipleRangeTypes,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editRangeType
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_category
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
    };
    
    theDatagrid = new GF_Datagrid($('#list-rangetype'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}