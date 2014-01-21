{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/view.png" alt=""/>{% trans %}TXT_SHOP_VIEW_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_SHOP_VIEW{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_SHOP_VIEW{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-shop-view"></div>
</div>
<script type="text/javascript">

function editView(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteView(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteView(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idview',
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
		}
	});
	
	var column_url = new GF_Datagrid_Column({
		id: 'url',
		caption: '{% trans %}TXT_URL{% endtrans %}',
		appearance: {
			width: 150
		}
	});
	
	var column_store = new GF_Datagrid_Column({
		id: 'store',
		caption: '{% trans %}TXT_STORE{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
		}
	});

	var options = {
		id: 'view',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idview',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllView,
			delete_row: deleteView,
			edit_row: editView,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editView
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_url,
			column_store
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			GF_Datagrid.ACTION_DELETE,
		]
	};
    
    theDatagrid = new GF_Datagrid($('#list-shop-view'), options);
		
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}