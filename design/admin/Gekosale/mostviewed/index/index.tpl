{% extends "layout.tpl" %}
{% block content %}
<h2><img
	src="{{ DESIGNPATH }}_images_panel/icons/modules/mostsearch-list.png"
	alt="" />{% trans %}TXT_MOST_VIEWED_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/delete" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_CLEAR{% endtrans %}</span></a></li>
</ul>
<div class="block">
<div id="list-mostviewed"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
      
   $(document).ready(function() {
   
   		var dataProvider;
   		
  		var column_id = new GF_Datagrid_Column({
			id: 'id',
			appearance: {
				width: 90,
				visible: false
			},
			caption: '{% trans %}TXT_ID{% endtrans %}',
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: '{% trans %}TXT_NAME{% endtrans %}',
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
		
		var column_qty = new GF_Datagrid_Column({
			id: 'qty',
			caption: '{% trans %}TXT_QUANTITY{% endtrans %}',
			sorting: {
				default_order: 'desc'
			}
		});
		
    var options = {
			id: 'product',
			mechanics: {
				key: 'id',
				default_sorting: 'qty',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllMostViewed,
			},
			columns: [
				column_id,
				column_name,
				column_category,
				column_qty
			],
    };
    
    theDatagrid = new GF_Datagrid($('#list-mostviewed'), options);
    
   });
   
   /*]]>*/
   
   
   
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}