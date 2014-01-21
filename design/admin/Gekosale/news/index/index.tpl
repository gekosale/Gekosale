{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/news-list.png" alt=""/>{% trans %}TXT_NEWS{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_NEWS{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_NEWS{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-news"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
   function deleteNews(dg, id) {
   		var oRow = theDatagrid.GetRow(id);
		var topic = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.topic +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteNews(p.id, p.dg);
		};
    new GF_Alert(topic, msg, func, true, params);
	 };
	 
	function deleteMultipleNews(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteNews(p.ids,p.dg);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function editNews(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
	 
	function enableNews(dg, id) {
		xajax_enableNews(dg, id);
	 };
	 
	 function disableNews(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_PUBLISH{% endtrans %}';
		var msg = '{% trans %}TXT_DISABLE_PUBLISH{% endtrans %} <strong>' + oRow.topic +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_disableNews(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };	
	 
	 function enableNews(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_PUBLISH{% endtrans %}';
		var msg = '{% trans %}TXT_ENABLE_PUBLISH{% endtrans %} <strong>' + oRow.topic +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_enableNews(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };
	 
   var theDatagrid;
   
   $(document).ready(function() {
   
  	 	var action_enableNews = new GF_Action({
			caption: '{% trans %}TXT_PUBLISH{% endtrans %}',
			action: enableNews,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
			condition: function(oR) { return oR['publish'] != '1'; }
		 });
		 
		 var action_disableNews= new GF_Action({
			caption: '{% trans %}TXT_NOT_PUBLISH{% endtrans %}',
			action: disableNews,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/off.png',
			condition: function(oR) { return oR['publish'] == '1'; }
		 });
		
		var column_id = new GF_Datagrid_Column({
			id: 'idnews',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_topic = new GF_Datagrid_Column({
			id: 'topic',
			caption: '{% trans %}TXT_TOPIC{% endtrans %}',
			appearance: {
				width: 140,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_startdate = new GF_Datagrid_Column({
			id: 'startdate',
			caption: '{% trans %}TXT_START_DATE{% endtrans %}',
			appearance: {
				width: 140,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_enddate = new GF_Datagrid_Column({
			id: 'enddate',
			caption: '{% trans %}TXT_END_DATE{% endtrans %}',
			appearance: {
				width: 140,
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_summary = new GF_Datagrid_Column({
			id: 'summary',
			caption: '{% trans %}TXT_NEWS_SUMMARY{% endtrans %}',
			appearance: {
				width: 140,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		 
    	var options = {
			id: 'news',
			mechanics: {
				key: 'idnews',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllNews,
				delete_row: deleteNews,
				edit_row: editNews,
				delete_group: deleteMultipleNews,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editNews
				{% endif %}
			},
			columns: [
				column_id,
				column_topic,
				column_summary,
				column_startdate,
				column_enddate,
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				action_enableNews,
				action_disableNews,
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				action_enableNews,
				action_disableNews,
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
    };
    
    theDatagrid = new GF_Datagrid($('#list-news'), options);
	
	});
   
   /*]]>*/
   
   
   
</script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}