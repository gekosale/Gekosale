{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/language-list.png" alt=""/>{% trans %}TXT_LANGUAGES_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_LANGUAGE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_LANGUAGE{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-languages"></div>
</div>
<script type="text/javascript">



/*<![CDATA[*/
function processFlag(oRow) {
	if (oRow.flag != '') {
		oRow.flag = '<img src="' + GCore.DESIGN_PATH + '_images_common/icons/languages/'+oRow.flag+'" style="vertical-align: middle;" alt="{% trans %}TXT_VIEW_THUMB{% endtrans %}"/>';
	}
	return oRow;
};
	 
function editLanguage(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteLanguage(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.translation +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteLanguage(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;

$(document).ready(function() {

	var action_removableLanguage = new GF_Action({
		caption: '{% trans %}TXT_DELETE{% endtrans %}',
		action: deleteLanguage,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/delete.png',
		condition: function(oR) { return oR['idlanguage'] != '1'; }
	});
		 
	var column_id = new GF_Datagrid_Column({
		id: 'idlanguage',
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
			width: 90,
		},
	});

	var column_translation = new GF_Datagrid_Column({
		id: 'translation',
		caption: '{% trans %}TXT_TRANSLATION{% endtrans %}',
		appearance: {
			width: 90,
		},
	});

	var column_currency = new GF_Datagrid_Column({
		id: 'currency',
		caption: '{% trans %}TXT_DEFAULT_LANGUAGE_CURRENCY{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
			{{ datagrid_filter.currency }}
			],
		}

	});

	var column_flag = new GF_Datagrid_Column({
		id: 'flag',
		caption: '{% trans %}TXT_LANGUAGE_FLAG{% endtrans %}',
		appearance: {
			width: 30,
			no_title: true
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
		id: 'language',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idlanguage',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllLanguage,
			delete_row: deleteLanguage,
			edit_row: editLanguage,
			process: processFlag,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editLanguage
			{% endif %}
		},
		columns: [
			column_id,
			column_flag,
			column_name,
			column_translation,
			column_currency,
			column_adddate,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removableLanguage
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removableLanguage
		]
	};

	theDatagrid = new GF_Datagrid($('#list-languages'), options);

});

/*]]>*/



  </script>
{% endblock %}