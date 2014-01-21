{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/category-edit.png" alt=""/>{% trans %}TXT_TEMPLATE_EDITOR{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-pagescheme"></div>
</div>

<script type="text/javascript">

function processScheme(row) {

	if (row.thumb != '') {
		row.thumb = '<a title="" href="' + row.thumb + '" class="show-thumb"><img src="{{ DESIGNPATH }}_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" /></a>';
	}
	return {
		idpagescheme: row.idpagescheme,
		templatefolder: row.templatefolder,
		name: row.name,
		def: (row.def == 1) ? '{% trans %}TXT_YES{% endtrans %}' : '{% trans %}TXT_NO{% endtrans %}',
		thumb: row.thumb,
	};
};

function dataLoaded(dDg) {
	dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
};

function editPagescheme(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + oRow.templatefolder + '';
};

function exportScheme(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + oRow.idpagescheme + '';
};

function deletePagescheme(dg, id) {
	var systemTemplates = ["wellcommerce_tech", "wellcommerce_fashion"];
 	var oRow = theDatagrid.GetRow(id);
 	if($.inArray(oRow.templatefolder, systemTemplates) > -1) {
 		return GError('Nie możesz skasować domyślnych szablonów WellCommerce.');
 	}
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeletePagescheme(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function setDefaultPagescheme(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DEFAULT{% endtrans %}';
	var msg = '{% trans %}TXT_SET_DEFAULT{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_setDefaultPagescheme(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};	
	
var theDatagrid;
	 
$(document).ready(function() {
	   
	var action_setDefaultPagescheme = new GF_Action({
		caption: '{% trans %}TXT_SET_DEFAULT{% endtrans %}',
		action: setDefaultPagescheme,
	   	img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
	   	condition: function(oR) { return oR['def'] == '0'; }
	});

	var action_removableScheme = new GF_Action({
		caption: '{% trans %}TXT_DELETE{% endtrans %}',
		action: deletePagescheme,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/delete.png',
		condition: function(oR) { return oR['def'] != '1'; }
	});
		
	var action_exportScheme = new GF_Action({
		caption: '{% trans %}TXT_EXPORT{% endtrans %}',
		action: exportScheme,
		img: '{{ DESIGNPATH }}_images_panel/icons/buttons/duplicate.png',
	});
		
	var column_id = new GF_Datagrid_Column({
		id: 'idpagescheme',
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
		editable: true,
		appearance: {
			width: 440,
		},
	});

	var column_thumb = new GF_Datagrid_Column({
		id: 'thumb',
		caption: '{% trans %}TXT_PREVIEW{% endtrans %}',
		appearance: {
			width: 30,
			no_title: true
		}
	});
	
	var column_default = new GF_Datagrid_Column({
		id: 'def',
		caption: '{% trans %}TXT_DEFAULT{% endtrans %}',
		appearance: {
			width: 20,
		}
	});
		
    var options = {
		id: 'pagescheme',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idpagescheme',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllPagescheme,
			delete_row: deletePagescheme,
			edit_row: editPagescheme,
			process: processScheme,
			loaded: dataLoaded,
			update_row: function(sId, oRow) {
				xajax_doUpdateScheme(oRow.idpagescheme, oRow.name);
				theDatagrid.LoadData();
			},
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editPagescheme
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_thumb,
			column_default,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removableScheme,
			{% if viewid > 0 %}
			action_setDefaultPagescheme,
			{% endif %}
			action_exportScheme
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removableScheme,
			{% if viewid > 0 %}
			action_setDefaultPagescheme,
			{% endif %}
			action_exportScheme
		]
	};
    
    theDatagrid = new GF_Datagrid($('#list-pagescheme'), options);
});
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}