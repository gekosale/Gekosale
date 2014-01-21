{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/similarproduct-list.png" alt=""/>{% trans %}TXT_BUY_ALSO_LIST{% endtrans %}</h2>
<div class="block">
	<div id="list-buyalso"></div>
</div>
<script type="text/javascript">

function viewBuyalso(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + id + '';
};

var theDatagrid;
	 
$(document).ready(function() {

	var column_id = new GF_Datagrid_Column({
		id: 'productid',
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
	 
    var options = {
		id: 'productid',
		mechanics: {
			key: 'productid',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllBuyalso,
			edit_row: viewBuyalso,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: viewBuyalso
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
		]
    };

    theDatagrid = new GF_Datagrid($('#list-buyalso'), options);
		
});

</script>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}