{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/view.png" alt=""/>Kupony rabatowe</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD{% endtrans %}</span></a></li>
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/generate" class="button" title="{% trans %}TXT_GENERATE_COUPONS{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_GENERATE_COUPONS{% endtrans %}</span></a></li>
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/export" class="button" title="{% trans %}TXT_EXPORT{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_EXPORT{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="list-points-rewards"></div>
</div>

<script type="text/javascript">

	 function editCoupons(dg, id) {
    location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
	 };
function deleteCoupons(dg, id) {
		var topic = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteCoupons(p.dg, p.id);
		};
    new GF_Alert(topic, msg, func, true, params);
	 };

	 function deleteCoupons(dg, id) {
		var topic = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + id + '?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteCoupons(p.dg, p.id);
		};
    new GF_Alert(topic, msg, func, true, params);
	 };

	function deleteMultipleCoupons(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids
		};
		var func = function(p) {
			return xajax_doDeleteCoupons(p.dg, p.ids);
		};
    new GF_Alert(title, msg, func, true, params);
	 };


	var theDatagrid;

   $(document).ready(function() {

		var column_id = new GF_Datagrid_Column({
			id: 'id',
			caption: '{% trans %}TXT_ID{% endtrans %}',
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_code = new GF_Datagrid_Column({
			id: 'code',
			caption: 'Kod',
			appearance: {
				width: 70
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT

			}
		});

		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: 'Nazwa',
			appearance: {
				width: 70
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});

		var column_datefrom = new GF_Datagrid_Column({
			id: 'datefrom',
			caption: '{% trans %}TXT_DATE_FROM{% endtrans %}',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_dateto = new GF_Datagrid_Column({
			id: 'dateto',
			caption: '{% trans %}TXT_DATE_TO{% endtrans %}',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_globalqty = new GF_Datagrid_Column({
			id: 'globalqty',
			caption: 'Ilość kuponów',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});

		var column_used = new GF_Datagrid_Column({
			id: 'used',
			caption: 'Wykorzystanych',
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});


    	var options = {
			id: 'coupons',
			appearance: {
				column_select: true
			},
			mechanics: {
				key: 'id'
			},
			event_handlers: {
				load: xajax_LoadAllCoupons,
				edit_row: editCoupons,
				delete_row: deleteCoupons,
				delete_group: deleteMultipleCoupons,
				{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
				click_row: editCoupons
				{% endif %}
			},
			columns: [
				column_id,
				column_name,
				column_code,
				column_datefrom,
				column_dateto,
				column_globalqty,
				column_used
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

    theDatagrid = new GF_Datagrid($('#list-points-rewards'), options);

	 });

   /*]]>*/



  </script>
{% endblock %}