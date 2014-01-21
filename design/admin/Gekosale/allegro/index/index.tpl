{% extends "layout.tpl" %}
{% block content %}
{% if errormsg %}
	{% include "allegro/error.tpl" %}
{% else %}
<h2>Lista aukcji Allegro</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span>Nowa
				aukcja</span></a></li>
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/view" id="refresh"
		class="button"><span>Synchronizuj aukcje</span></a></li>
</ul>
<div class="block">
	<div id="list-auctions"></div>
</div>
<script type="text/javascript">
function deleteAuction(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = '{% trans %}TXT_DELETE{% endtrans %}';
	var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + oRow.firstname + ' '+ oRow.surname +'</strong> ?';
	var params = {
		dg: dg,
		id: id
	};
	var func = function(p) {
		return xajax_doDeleteClient(p.dg, p.id);
	};
    new GF_Alert(title, msg, func, true, params);
};

function viewAuction(dg, id) {
	var oRow = theDatagrid.GetRow(id);
	window.open('http://www.allegro.pl/show_item.php?item=' + oRow.itemid);
};

function finishAuction(dg, id) {
 	var oRow = theDatagrid.GetRow(id);
	var title = 'Zakończenie aukcji przed czasem';
	var msg = 'Czy na pewno chcesz zakończyć aukcję <strong>' + oRow.itemid +'</strong>?';
	var params = {
		dg: dg,
		id: oRow.itemid
	};
	var func = function(p) {
		return xajax_finishAuction(p.dg, p.id);
	};
	new GF_Alert(title, msg, func, true, params);
 };

var theDatagrid;

function processAuction(row) {

	if (row.status == 'sold') {
		row.status = 'Sprzedane';
	}
	if (row.status == 'notsold') {
		row.status = 'Niesprzedane';
	}
	if (row.status == 'future') {
		row.status = 'Zaplanowane';
	}
	if (row.status == 'sell') {
		row.status = 'Sprzedaję';
	}
	return row;
};

$(document).ready(function() {

	var column_id = new GF_Datagrid_Column({
		id: 'idauction',
		caption: '{% trans %}TXT_ID{% endtrans %}',
		appearance: {
			width: 90,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_itemid = new GF_Datagrid_Column({
		id: 'itemid',
		caption: '{% trans %}TXT_CLIENTORDER_VALUE{% endtrans %}',
		appearance: {
			width: 40,
			visible: false
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_title = new GF_Datagrid_Column({
		id: 'title',
		caption: 'Tytuł',
		appearance: {
			width: 200,
			align: GF_Datagrid.ALIGN_LEFT
		},
		filter: {
			type: GF_Datagrid.FILTER_INPUT,
		}
	});

	var column_quantity = new GF_Datagrid_Column({
		id: 'quantity',
		caption: 'Wystawionych sztuk',
		appearance: {
			width: 60,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_buyprice = new GF_Datagrid_Column({
		id: 'buyprice',
		caption: 'Cena Kup Teraz',
		appearance: {
			width: 60,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_startdate = new GF_Datagrid_Column({
		id: 'startdate',
		caption: 'Data rozpoczęcia',
		appearance: {
			width: 60,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_enddate = new GF_Datagrid_Column({
		id: 'enddate',
		caption: 'Data zakończenia',
		appearance: {
			width: 60,
		},
		filter: {
			type: GF_Datagrid.FILTER_BETWEEN,
		}
	});

	var column_status = new GF_Datagrid_Column({
		id: 'status',
		caption: 'Status',
		appearance: {
			width: 60
		},
		filter: {
			type: GF_Datagrid.FILTER_SELECT,
			options: [{"id":"","caption":""},{"id":"sold","caption":"Sprzedane"},{"id":"notsold","caption":"Niesprzedane"},{"id":"sell","caption":"Sprzedaję"},{"id":"future","caption":"Zaplanowane"}]
		}
	});

	var action_finish = new GF_Action({
		img: '{{ DESIGNPATH }}/_images_panel/icons/datagrid/stop.png',
		caption: 'Zakończ aukcję przed czasem',
		action: finishAuction
	});

    var options = {
		id: 'auction',
		appearance: {
			column_select: false
		},
		mechanics: {
			key: 'idauction',
			rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }}
		},
		event_handlers: {
			load: xajax_LoadAllAuction,
			click_row: viewAuction,
			process: processAuction
		},
		columns: [
			column_id,
			column_itemid,
			column_title,
			column_status,
			column_quantity,
			column_buyprice,
			column_startdate,
			column_enddate
		],
		row_actions: [
			action_finish
		],
		context_actions: [
			action_finish
		]
	};

    theDatagrid = new GF_Datagrid($('#list-auctions'), options);

});
</script>
{% endif %}
{% endblock %}
