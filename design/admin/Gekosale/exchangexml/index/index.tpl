{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/rulescart-list.png" alt=""/>{% trans %}TXT_EXTENDED_EXCHANGE{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_EXCHANGE_ADD_OPERATION{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_EXCHANGE_ADD_OPERATION{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-exchangexml"></div>
</div>
<script type="text/javascript">



   /*<![CDATA[*/

   	 function editOperation(dg, id) {
   		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };

	function deleteOperation(dg, id) {
			var oRow = theDatagrid.GetRow(id);
			var topic = '{% trans %}TXT_DELETE{% endtrans %}';
			var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
			var params = {
				dg: dg,
				id: id
			};
			var func = function(p) {
				return xajax_doDeleteOperation(p.dg, p.id);
			};
			new GF_Alert(topic, msg, func, true, params);
	 };

	function runOperation(dg, id) {
   		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/runoperation/' + id + '';
	};

	function downloadFile(dg, id) {
   		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/download/' + id + '';
	};

	function info(dg, id) {
   		location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/info/' + id + '';
	};

	 var theDatagrid;

   $(document).ready(function() {

  	 	var action_runOperation = new GF_Action({
			caption: '{% trans %}TXT_EXECUTE{% endtrans %}',
			action: runOperation,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png'
		 });

  	 	var action_downloadFile = new GF_Action({
			caption: '{% trans %}TXT_DOWNLOAD_FILE{% endtrans %}',
			action: downloadFile,
			img: '{{ DESIGNPATH }}_images_panel/icons/buttons/save.png'
		 });

  	 	var action_info = new GF_Action({
			caption: '{% trans %}TXT_ADDITIONAL_INFORMATION{% endtrans %}',
			action: info,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/description.png'
		 });

		var column_id = new GF_Datagrid_Column({
			id: 'idexchange',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: '{% trans %}TXT_INTEGRATION_NAME{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_datatype = new GF_Datagrid_Column({
			id: 'datatype',
			caption: '{% trans %}TXT_BACKUP_TYPE{% endtrans %}',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_ie = new GF_Datagrid_Column({
			id: 'ie',
			caption: '{% trans %}TXT_IMPORT_EXPORT{% endtrans %}',
			appearance: {
				width: 120
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_url = new GF_Datagrid_Column({
			id: 'url',
			caption: '{% trans %}TXT_ADDRESS{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_lastdate = new GF_Datagrid_Column({
			id: 'lastdate',
			caption: '{% trans %}TXT_LAST_EXECUTION{% endtrans %}',
			appearance: {
				width: 150
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_status = new GF_Datagrid_Column({
			id: 'status',
			caption: '{% trans %}TXT_STATUS{% endtrans %}',
			appearance: {
				width: 80
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});


    var options = {
			id: 'idexchange',
			mechanics: {
				key: 'idexchange',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllOperations,
				delete_row: deleteOperation,
				edit_row: editOperation,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editOperation
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_ie,
				column_datatype,
				column_url,
				column_lastdate,
				column_status,
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE,
				action_runOperation,
				action_downloadFile,
				action_info
			],
			group_actions: [
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE,
				action_runOperation,
				action_downloadFile,
				action_info
			]
    };

    theDatagrid = new GF_Datagrid($('#list-exchangexml'), options);

   });

   /*]]>*/



  </script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}