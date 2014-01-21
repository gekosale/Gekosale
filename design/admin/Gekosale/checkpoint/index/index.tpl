<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/checkpoint.png" alt=""/>{% trans %}TXT_CHECKPOINTS{% endtrans %}</h2>

<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_CHECKPOINTS{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_CHECKPOINTS{% endtrans %}</span></a></li>
</ul>

<div class="block">
	<div id="list-checkpoint"></div>
</div>

<script type="text/javascript">
   
   
   /*<![CDATA[*/
   
   	function restore(dg, column_date){
		//Not done yet. Sorry  :(
	};

	function deleteCheckpoint(dg, id){
   		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} <strong>' + id +'</strong> ?';
		var params = {
			dg: dg,
			id: id
		};
		var func = function(p) {
			return xajax_deleteCheckpoint(p.id);
		};
    new GF_Alert(title, msg, func, true, params);
	 };

	 function deleteCheckpoints(dg, ids) {
		var title = '{% trans %}TXT_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ' + ids.join(', ') + '?';
		var params = {
			dg: dg,
			ids: ids,
		};
		var func = function(p) {
			return xajax_deleteCheckpoint(p.ids);
		};
		new GF_Alert(title, msg, func, true, params);
	};
			
	 var dataProvider;
   $(document).ready(function() {
   
	   var column_id = new GF_Datagrid_Column({
			id: 'id',
			caption: '{% trans %}TXT_CHECKPOINTS{% endtrans %}',
			appearance: {
				visible: false
			}
		});
		
		var column_date = new GF_Datagrid_Column({
			id: 'date',
			caption: '{% trans %}TXT_CHECKPOINTS{% endtrans %}',
		});
		
		var column_type = new GF_Datagrid_Column({
			id: 'type',
			caption: '{% trans %}TXT_TYPE{% endtrans %}',
		});
			
	dataProvider = new GF_Datagrid_Data_Provider({
		key: 'date',
	}, {{ chkpoints }});

    var options = {
			id: 'chk',
			mechanics: {
				key: 'id',
				right_click_menu: false
			},
			event_handlers: {
				delete_row: deleteCheckpoint,
				delete_group: deleteCheckpoints,
				load: function(oRequest, sResponseHandler) {
				dataProvider.Load(oRequest, sResponseHandler);
				}
			},
			columns: [
				column_id,
				column_date,
				column_type
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
    
    var theDatagrid = new GF_Datagrid($('#list-checkpoint'), options);
    
   });
   
   /*]]>*/
   
   
   
  </script>
