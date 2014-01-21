{% extends "layout.tpl" %}
{% block content %}
<h2>{% trans %}TXT_CROSS_SELLS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_CROSS_SELL{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_CROSS_SELL{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-crosssell"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/

	 function editCrosssell(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
	 
	 function deleteCrosssell(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteCrosssell(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };

   function deleteMultipleCrosssells(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteCrosssell(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
	 
   $(document).ready(function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idcrosssell',
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
			caption: '{% trans %}TXT_PRODUCT{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_AUTOSUGGEST,
				source: xajax_GetNameSuggestions,
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
		
		var column_productcount = new GF_Datagrid_Column({
			id: 'productcount',
			caption: '{% trans %}TXT_PRODUCT_COUNT{% endtrans %}',
			appearance: {
				width: 130
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
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
			id: 'crosssell',
			mechanics: {
				key: 'idcrosssell',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllCrosssell,
				delete_row: deleteCrosssell,
				edit_row: editCrosssell,
				delete_group: deleteMultipleCrosssells,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editCrosssell
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_category,
				column_productcount,
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
    
    theDatagrid = new GF_Datagrid($('#list-crosssell'), options);
		
	 });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}