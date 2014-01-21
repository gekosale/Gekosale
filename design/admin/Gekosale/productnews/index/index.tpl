{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/productnews-list.png" alt=""/>{% trans %}TXT_PRODUCT_NEWS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-productnews"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
   function deleteProductNews(dg, id) {
   		var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteProductNews(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleroductNews(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteProductNews(p.dg,p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function disableProductNews(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DISABLE{% endtrans %}';
		var msg = '{% trans %}TXT_DISABLE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_disableProductNews(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };	
	 
	 function enableProductNews(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_ENABLE{% endtrans %}';
		var msg = '{% trans %}TXT_ENABLE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_enableProductNews(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
	 
   $(document).ready(function() {
   
  		 var action_enableProductNews = new GF_Action({
			caption: '{% trans %}TXT_ENABLE_PRODUCT_NEWS{% endtrans %}',
			action: enableProductNews,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
			condition: function(oR) { return oR['active'] != '1'; }
		 });
		 
		 var action_disableProductNews = new GF_Action({
			caption: '{% trans %}TXT_DISABLE_PRODUCT_NEWS{% endtrans %}',
			action: disableProductNews,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/off.png',
			condition: function(oR) { return oR['active'] == '1'; }
		 });
		
		var column_id = new GF_Datagrid_Column({
			id: 'idproductnew',
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
			appearance: {
				width: 150
			},
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
		
		var column_startdate = new GF_Datagrid_Column({
			id: 'startdate',
			caption: '{% trans %}TXT_START_DATE{% endtrans %}',
			appearance: {
				width: 140,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_enddate = new GF_Datagrid_Column({
			id: 'enddate',
			caption: '{% trans %}TXT_END_DATE{% endtrans %}',
			appearance: {
				width: 140,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
    	var options = {
			id: 'productnew',
			mechanics: {
				key: 'idproductnew',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllProductNews,
				delete_row: deleteProductNews,
				delete_group: xajax_doDeleteProductNews,
			},
			row_actions: [
				GF_Datagrid.ACTION_DELETE,
				action_enableProductNews,
				action_disableProductNews
			],
			columns: [
				column_id,
				column_name,
				column_category,
				column_adddate,
				column_startdate,
				column_enddate
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_DELETE,
				action_enableProductNews,
				action_disableProductNews
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-productnews'), options);
		
	 });
   
   
   
   
   
  </script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}