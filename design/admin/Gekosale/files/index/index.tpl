{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/category-list.png" alt=""/>{% trans %}TXT_FILES_LIST{% endtrans %}</h2>

<div class="block">
	<div id="list-files"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
	 
	 function processFile(oRow) {
		if (oRow.thumb != '') {
			oRow.thumb = '<a href="' + oRow.thumb + '" class="show-thumb"><img src="{{ DESIGNPATH }}_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" alt="{% trans %}TXT_VIEW_THUMB{% endtrans %}"/></a>';
		}
		if (oRow.path != '') {
			oRow.path = '<a href="' + oRow.path + '" target="_blank" class="path">' + oRow.path +'</a>';
		}
		return oRow;
	 };
	 
	 function dataLoaded(dDg) {
		dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
		dDg.m_jBody.find('.path').click(function(){
			window.open($(this).attr('href'));
		});
	 };
   
   function viewFiles(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/view/' + id + '';
   };
   
   function editFiles(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
   };
   
   function deleteFiles(dg, id){
   		var oRow = theDatagrid.GetRow(id);
   		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.filename +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteFiles(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	function deleteMultipleFiles(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteFiles(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
   
   $(document).ready(function() {
	  
	  var column_id = new GF_Datagrid_Column({
			id: 'idfile',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			sorting: {
				default_order: GF_Datagrid.SORT_DIR_DESC
			},
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumb',
			caption: '{% trans %}TXT_THUMB{% endtrans %}',
			appearance: {
				width: 30,
				no_title: true
			}
		});
		
		var column_filename = new GF_Datagrid_Column({
			id: 'filename',
			caption: '{% trans %}TXT_NAME{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_AUTOSUGGEST,
				source: xajax_GetFilenameSuggestions,
			},
			appearance: {
				width: 130,
			}
		});
		
		var column_path = new GF_Datagrid_Column({
			id: 'path',
			caption: '{% trans %}TXT_URL{% endtrans %}',
			appearance: {
				width: 230,
				no_title: true
			}
		});
		
		var column_fileextension = new GF_Datagrid_Column({
			id: 'fileextension',
			caption: '{% trans %}TXT_FILEEXTENSION{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{{ datagrid_filter.fileextension }}
				],
			},
			appearance: {
				width: 30,
			}
		});
		
		var options = {
			id: 'files',
			mechanics: {
				key: 'idfile',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllFiles,
				delete_row: deleteFiles,
				process: processFile,
				loaded: dataLoaded,
				delete_group: deleteMultipleFiles,
			},
			columns: [
				column_id,
				column_thumb,
				column_filename,
				column_path,
				column_fileextension,
			],
			row_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_DELETE
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-files'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}