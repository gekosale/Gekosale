{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/users-list.png" alt=""/>{% trans %}TXT_ADMINISTRATORS_LIST{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_USER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_USER{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-users"></div>
</div>

<script type="text/javascript">
function editUser(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteUser(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteUser(p.id, p.dg);
	};
	new GF_Alert(title, msg, func, true, params);
};

function enableUser(dg, id) {
	xajax_enableUser(dg, id);
};

function disableUser(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DISABLE{% endtrans %}';
	var msg = '{% trans %}TXT_DISABLE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_disableUser(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};	

function enableUser(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_ENABLE{% endtrans %}';
	var msg = '{% trans %}TXT_ENABLE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_enableUser(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;

GCore.OnLoad(function() {

	var action_enableUser = new GF_Action({
		caption: '{% trans %}TXT_ENABLE_USER{% endtrans %}',
		action: enableUser,
		img: '{{ DESIGNPATH }}_images_panel/datagrid/user-unactive.png',
		condition: function(oR) { return oR['active'] != '1'; }
	});

	var action_disableUser = new GF_Action({
		caption: '{% trans %}TXT_DISABLE_USER{% endtrans %}',
		action: disableUser,
		img: '{{ DESIGNPATH }}_images_panel/datagrid/user-active.png',
		condition: function(oR) { return oR['active'] == '1'; }
	});

	var action_removeUser = new GF_Action({
		caption: '{% trans %}TXT_DELETE{% endtrans %}',
		action: deleteUser,
		img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/delete.png',
		condition: function(oR) { return oR['iduser'] != '1'; }
	});
	
	var column_id = new GF_Datagrid_Column({
		id: 'iduser',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_firstname = new GF_Datagrid_Column({
		id: 'firstname',
		caption: '{% trans %}TXT_FIRSTNAME{% endtrans %}',
		appearance: {
			width: 140
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});

	var column_surname = new GF_Datagrid_Column({
		id: 'surname',
		caption: '{% trans %}TXT_SURNAME{% endtrans %}',
		appearance: {
			width: 140
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});

	var column_email = new GF_Datagrid_Column({
		id: 'email',
		caption: '{% trans %}TXT_EMAIL{% endtrans %}',
		appearance: {
			width: 180
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});

	var column_group = new GF_Datagrid_Column({
		id: 'groupname',
		caption: '{% trans %}TXT_GROUP_NAME{% endtrans %}',
		appearance: {
			width: 120
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [
				{{ datagrid_filter.groupnames }}
			],
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
		id: 'user',
		mechanics: {
			key: 'iduser',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllUser,
			delete_row: deleteUser,
			edit_row: editUser,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editUser
			{% endif %}
		},
		columns: [
			column_id,
			column_surname,
			column_firstname,
			column_group,
			column_email,
			column_adddate,
		],
		row_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removeUser,
			action_enableUser,
			action_disableUser
		],
		context_actions: [
			GF_Datagrid.ACTION_EDIT,
			action_removeUser,
			action_enableUser,
			action_disableUser
		]
	};

	theDatagrid = new GF_Datagrid($('#list-users'), options);

});
</script>
{% endblock %}