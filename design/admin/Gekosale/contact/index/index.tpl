{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/contact-list.png" alt=""/>{% trans %}TXT_CONTACTS_LIST{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_CONTACT{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_CONTACT{% endtrans %}</span></a></li>
</ul>
<div class="block">
	<div id="datagrid"></div>
</div>
<script type="text/javascript">
function editContact(dg, id) {
	location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + id + '';
};

function deleteContact(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.name +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteContact(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
};
	 
function deleteMultipleContact(dg, ids) {
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
	var params = {
		dg: dg,
		ids: ids
	};
	var func = function(p) {
		return xajax_doDeleteContact(p.dg, p.ids);
	};
	new GF_Alert(title, msg, func, true, params);
};

var theDatagrid;
	 
$(document).ready(function() {
		
	var column_id = new GF_Datagrid_Column({
		id: 'idcontact',
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
	
	var column_email = new GF_Datagrid_Column({
		id: 'email',
		caption: '{% trans %}TXT_EMAIL{% endtrans %}',
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});
	
	var column_phone = new GF_Datagrid_Column({
		id: 'phone',
		caption: '{% trans %}TXT_PHONE{% endtrans %}'
	});
	
	var column_fax = new GF_Datagrid_Column({
		id: 'fax',
		caption: '{% trans %}TXT_FAX{% endtrans %}'
	});
	
	var column_address = new GF_Datagrid_Column({
		id: 'address',
		caption: '{% trans %}TXT_ADDRESS{% endtrans %}',
		appearance: {
			width: 240
		}
	});
	
	var column_street = new GF_Datagrid_Column({
		id: 'street',
		caption: '{% trans %}TXT_STREET{% endtrans %}',
		appearance: {
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetStreetSuggestions,
		}
	});
	
	var column_streetno = new GF_Datagrid_Column({
		id: 'streetno',
		caption: '{% trans %}TXT_STREETNO{% endtrans %}',
		appearance: {
			visible: false
		}
	});
	
	var column_placeno = new GF_Datagrid_Column({
		id: 'placeno',
		caption: '{% trans %}TXT_PLACENO{% endtrans %}',
		appearance: {
			visible: false
		}
	});
	
	var column_placename = new GF_Datagrid_Column({
		id: 'placename',
		caption: '{% trans %}TXT_PLACENAME{% endtrans %}',
		appearance: {
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_AUTOSUGGEST,
			source: xajax_GetPlacenameSuggestions,
		}
	});
	
	var column_postcode = new GF_Datagrid_Column({
		id: 'postcode',
		caption: '{% trans %}TXT_POSTCODE{% endtrans %}',
		appearance: {
			visible: false
		}
	});

	var options = {
		id: 'contact',
		mechanics: {
			key: 'idcontact',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllContact,
			delete_row: deleteContact,
			edit_row: editContact,
			delete_group: deleteMultipleContact,
			{% if globalsettings.interface.datagrid_click_row_action == 'edit' %}
			click_row: editContact
			{% endif %}
		},
		columns: [
			column_id,
			column_name,
			column_email,
			column_address,
			column_phone,
			column_fax,
			column_street,
			column_streetno,
			column_placeno,
			column_placename,
			column_postcode
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
    
    theDatagrid = new GF_Datagrid($('#datagrid'), options);
    
});
</script>
{% endblock %}

{% block sticky %}
{% include sticky %}
{% endblock %}