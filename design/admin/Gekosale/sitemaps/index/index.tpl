{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/translation-list.png" alt=""/>{% trans %}TXT_SITEMAPS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_SITEMAPS{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_SITEMAPS{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-sitemaps"></div>
</div>

<script type="text/javascript">

function deleteSitemaps(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var topic = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteSitemaps(p.dg, p.id);
	};
    new GF_Alert(topic, msg, func, true, params);
};
	 
function deleteMultipleSitemaps(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteSitemaps(p.dg, p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
function editSitemaps(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};
	 
function refreshSitemaps(dg, id) {
	xajax_refreshSitemaps(dg, id);
};

function viewSitemaps(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	window.open(oRow.link);
};
	 
var theDatagrid;
   
$(document).ready(function() {
   
	var action_refreshSitemaps = new GF_Action({
		caption: '{% trans %}TXT_REFRESH{% endtrans %}',
		action: refreshSitemaps,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/refresh.png'
	});
	
	var column_id = new GF_Datagrid_Column({
		id: 'idsitemaps',
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
		appearance: {
			width: 60
		}
	});
		
	var column_pingserver = new GF_Datagrid_Column({
		id: 'pingserver',
		caption: '{% trans %}TXT_SITEMAPS_PINGSERVER{% endtrans %}',
		appearance: {
			width: 160,
			visible: false
		},
	});
		
	var column_lastupdate = new GF_Datagrid_Column({
		id: 'lastupdate',
		caption: '{% trans %}TXT_SITEMAPS_LASTUPDATE{% endtrans %}',
		appearance: {
			width: 50
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT
		}
	});
		
   	var options = {
		id: 'sitemaps',
		mechanics: {
			key: 'idsitemaps',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllSitemaps,
			delete_row: deleteSitemaps,
			edit_row: editSitemaps,
			delete_group: deleteMultipleSitemaps,
			view_row: viewSitemaps,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editSitemaps
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_pingserver,
			column_lastupdate
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_refreshSitemaps,
			GF_Datagrid.ACTION_VIEW,
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_refreshSitemaps,
			GF_Datagrid.ACTION_VIEW,
			GF_Datagrid.ACTION_DELETE
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
	};
    
    theDatagrid = new GF_Datagrid($('#list-sitemaps'), options);
	
});
</script>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}