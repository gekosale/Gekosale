{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-list.png" alt=""/>{% trans %}TXT_NEWSLETTER_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_NEWSLETTER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_NEWSLETTER{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-newsletter"></div>
</div>

<script type="text/javascript">
   
   
   
   /*<![CDATA[*/
   
    function editNewsletter(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
   
	 function deleteNewsletter(dg, id) {
	 	var oRow = theDatagrid.GetRow(id);
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteNewsletter(p.dg, p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
	 
	 function deleteMultipleNewsletter(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteNewsletter(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };
   
	 var theDatagrid;
	 
   $(document).ready(function() {

		var column_id = new GF_Datagrid_Column({
			id: 'idnewsletter',
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
				width: 120,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_subject = new GF_Datagrid_Column({
			id: 'subject',
			caption: '{% trans %}TXT_SUBJECT{% endtrans %}',
			appearance: {
				width: 120,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});

		var column_email = new GF_Datagrid_Column({
			id: 'email',
			caption: '{% trans %}TXT_EMAIL{% endtrans %}',
			appearance: {
				width: 120,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
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
			id: 'newsletter',
			mechanics: {
				key: 'idnewsletter',
				rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
			},
			event_handlers: {
				load: xajax_LoadAllNewsletter,
				delete_row: deleteNewsletter,
				edit_row: editNewsletter,
				delete_group: deleteMultipleNewsletter,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editNewsletter
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_subject,
				column_email,
				column_adddate,
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			]
    };
    
    theDatagrid = new GF_Datagrid($('#list-newsletter'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}