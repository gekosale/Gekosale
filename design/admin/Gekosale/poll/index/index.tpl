{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/poll-list.png" alt=""/>{% trans %}TXT_POLLS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_POLL{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_POLL{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-polls"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
   function deletePoll(dg, id) {
   		var oRow = theDatagrid.GetRow(id);
		var topic = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.questions +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeletePoll(p.dg, p.id);
		};
    new GF_Alert(topic, msg, func, true, params);
	 };
	 
	 function deleteMultiplePolls(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeletePoll(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function editPoll(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
	 
	 function enableNews(dg, id) {
		xajax_enableNews(dg, id);
	 };
	 
	 function disablePoll(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_PUBLISH{% endtrans %}';
		var msg = '{% trans %}TXT_DISABLE_PUBLISH{% endtrans %} <strong>' + oRow.questions +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_disablePoll(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };	
	 
	 function enablePoll(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_PUBLISH{% endtrans %}';
		var msg = '{% trans %}TXT_ENABLE_PUBLISH{% endtrans %} <strong>' + oRow.questions +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_enablePoll(p.dg, p.id);
		};
		new GF_Alert(title, msg, func, true, params);
	 };

	 var theDatagrid;
	 
   $(document).ready(function() {
   
  	 var action_enablePoll = new GF_Action({
			caption: '{% trans %}TXT_PUBLISH{% endtrans %}',
			action: enablePoll,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/on.png',
			condition: function(oR) { return oR['publish'] != '1'; }
		 });
		 
		 var action_disablePoll= new GF_Action({
			caption: '{% trans %}TXT_NOT_PUBLISH{% endtrans %}',
			action: disablePoll,
			img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/off.png',
			condition: function(oR) { return oR['publish'] == '1'; }
		 });
		
		var column_id = new GF_Datagrid_Column({
			id: 'idpoll',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_questions = new GF_Datagrid_Column({
			id: 'questions',
			caption: '{% trans %}TXT_QUESTIONS{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_AUTOSUGGEST,
				source: xajax_GetQuestionsSuggestions,
			}
		});
		
		var column_votes = new GF_Datagrid_Column({
			id: 'votes',
			caption: '{% trans %}TXT_ANSWERS_DATA{% endtrans %}',
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
    	var options = {
			id: 'poll',
			mechanics: {
				key: 'idpoll',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllPoll,
				delete_row: deletePoll,
				edit_row: editPoll,
				delete_group: deleteMultiplePolls,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editPoll
				{% endif %}
			},
			columns: [
				column_id,
				column_questions,
				column_votes,
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE,
				action_enablePoll,
				action_disablePoll
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE,
				action_enablePoll,
				action_disablePoll
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
    };
    
    theDatagrid = new GF_Datagrid($('#list-polls'), options);
		
	 });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}