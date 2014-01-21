{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/contact-list.png" alt=""/>Klucze licencyjne</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/xls.png" alt=""/>Importuj klucze z CSV</span></a></li>
</ul>

<div class="block">
	<div id="list-licence"></div>
</div>

<script type="text/javascript">  
   /*<![CDATA[*/
   
   function deleteLicence(dg, id) {
   		var oRow = theDatagrid.GetRow(id);
		var topic = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = 'Czy chcesz skasować wszystkie klucze przypisane do <strong>' + oRow.name +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_doDeleteLicence(p.id, p.dg);
		};
    new GF_Alert(topic, msg, func, true, params);
	 };
	 
	function editLicence(dg, id) {
   	 location.href = '{{ URL }}product/edit/' + id + '';
	};
	 
	 
   var theDatagrid;
   
   $(document).ready(function() {
   
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: 'Nazwa produktu',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_id = new GF_Datagrid_Column({
			id: 'productid',
			caption: 'ID',
			appearance: {
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_ean = new GF_Datagrid_Column({
			id: 'ean',
			caption: 'EAN',
			filter: {
				type: GF_Datagrid.FILTER_INPUT,
			}
		});
		
		var column_keystotal = new GF_Datagrid_Column({
			id: 'keystotal',
			caption: 'Kluczy',
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
		var column_keyssold = new GF_Datagrid_Column({
			id: 'keyssold',
			caption: 'Sprzedanych',
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN,
			}
		});
		
    	var options = {
			id: 'licence',
			mechanics: {
				key: 'productid'
			},
			event_handlers: {
				load: xajax_LoadAllLicence,
				delete_row: deleteLicence,
				edit_row: editLicence,
			},
			columns: [
				column_id,
				column_name,
				column_ean,
				column_keystotal,
				column_keyssold,				
			],
			row_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
			context_actions: [
				GF_Datagrid.ACTION_EDIT,
				GF_Datagrid.ACTION_DELETE
			],
    };
    
    theDatagrid = new GF_Datagrid($('#list-licence'), options);
	
	});
   
   /*]]>*/
</script>
{% endblock %}