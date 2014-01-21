{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/translation-list.png" alt=""/>{% trans %}TXT_CONTROLLER_SEO_LIST{% endtrans %}</h2>
<div class="block">
	<div id="list-controllerseo"></div>
</div>

<script type="text/javascript">

function editControllerSeo(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idcontroller',
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
		
	var column_translation = new GF_Datagrid_Column({
		id: 'translation',
		editable: true,
		caption: '{% trans %}TXT_TRANSLATION{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetTranslationSuggestions,
		}
	});

    var options = {
		id: 'controller',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idcontroller',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllControllerSeo,
			edit_row: editControllerSeo,
			update_row: function(sId, oRow) {
				xajax_doUpdateControllerSeo(sId, oRow.translation);
				theDatagrid.LoadData();
			},
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editControllerSeo
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_translation,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT
		]
    };
    
    theDatagrid = new GF_Datagrid($('#list-controllerseo'), options);
		
});
</script>
{% endblock %}