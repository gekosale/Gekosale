{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/collection-list.png" alt=""/>{% trans %}TXT_COLLECTIONS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_COLLECTION{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_COLLECTION{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-collections"></div>
</div>

<script type="text/javascript">

function editCollection(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};
		
function deleteCollection(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteCollection(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleCollections(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteCollection(p.dg, p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idcollection',
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

	var column_producer = new GF_Datagrid_Column({
		id: 'producer',
		caption: '{% trans %}TXT_PRODUCER{% endtrans %}',
		appearance: {
			width: 260
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{{ datagrid_filter.producer }}
			],
		}
	});
	
	var options = {
		id: 'collection',
		mechanics: {
			key: 'idcollection',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllCollection,
			delete_row: deleteCollection,
			edit_row: editCollection,
			delete_group: deleteMultipleCollections,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editCollection
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_producer,
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
			GF_Datagrid.ACTION_DELETE,
		]
	};
    
    theDatagrid = new GF_Datagrid($('#list-collections'), options);
    
});
</script>
{% endblock %}