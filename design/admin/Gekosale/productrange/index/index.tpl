{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/productrange-list.png" alt=""/>{% trans %}TXT_PRODUCTRANGES_LIST{% endtrans %}</h2>

<div class="block">
	<div id="list-productranges"></div>
</div>

<script type="text/javascript">

function editTransmail(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteProductRange(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.nick +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteProductRange(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleProductRange(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteProductRange(p.dg, p.ids);
	};
    new GF_Alert(title, msg, func, true, params);
};

function enableOpinion(dg, id) {
	xajax_enableOpinion(dg, id);
};
	
function disableOpinion(dg, id) {
	xajax_disableOpinion(dg, id);
};
		 
var theDatagrid;
	 
$(document).ready(function() {

	var action_enableOpinion = new GF_Action({
		caption: '{% trans %}TXT_PUBLISH{% endtrans %}',
		action: enableOpinion,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
		condition: function(oR) { return oR['enable'] != '1'; }
	});
		 
	var action_disableOpinion = new GF_Action({
		caption: '{% trans %}TXT_NOT_PUBLISH{% endtrans %}',
		action: disableOpinion,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/off.png',
		condition: function(oR) { return oR['enable'] == '1'; }
	});

	var column_id = new GF_Datagrid_Column({
		id: 'idproductreview',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 60,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});
		
	var column_nick = new GF_Datagrid_Column({
		id: 'nick',
		caption: '{% trans %}TXT_NICK{% endtrans %}',
		appearance: {
			width: 60
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
		
	var column_client = new GF_Datagrid_Column({
		id: 'client',
		caption: '{% trans %}TXT_CLIENT{% endtrans %}',
		appearance: {
			width: 60
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
		
	var column_productname = new GF_Datagrid_Column({
		id: 'productname',
		caption: '{% trans %}TXT_PRODUCT{% endtrans %}',
		appearance: {
			width: 90
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
	
	var column_review = new GF_Datagrid_Column({
		id: 'review',
		caption: '{% trans %}TXT_CONTENT{% endtrans %}',
		appearance: {
			width: 190,
			align: GF_Datagrid.ALIGN_LEFT
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
		
	var column_rating = new GF_Datagrid_Column({
		id: 'rating',
		caption: '{% trans %}TXT_AVERAGE_OPINION{% endtrans %}',
		appearance: {
			width: 60
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

    var options = {
		id: 'productrange',
		mechanics: {
			key: 'idproductreview',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllProductRange,
			delete_row: deleteProductRange,
			edit_row: editTransmail,
			delete_group: deleteMultipleProductRange,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editTransmail
			{% endif %}
		},
		columns: [
			column_id,
			column_nick,
			column_client,
			column_productname,
			column_review,
			column_rating
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_enableOpinion,
			action_disableOpinion,
			GF_Datagrid.ACTION_DELETE
		],
		group_actions: [
			GF_Datagrid.ACTION_DELETE
		],
		context_actions: [
			GF_Datagrid.ACTION_DELETE
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-productranges'), options);
		
});
</script>
{% endblock %}