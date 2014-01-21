<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/updater.png"	alt="" />{% trans %}TXT_GEKOLAB{% endtrans %}</h2>
{% if channelError is defined %}
<div class="block">
<div id="list-update"><p>{{ channelError }}</p></div>
</div>
{% else %}
<div class="block">
<div id="list-update"></div>
</div>
<script type="text/javascript">

	
		
		/*<![CDATA[*/

		var dataProvider;
		GCore.OnLoad(function() {
			
		

			function installPackage(dg, column_uniquename) {
				var oPackage = GF_Datagrid.ReturnInstance(dg).GetRow(column_uniquename);
				location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/add/' + oPackage.package + '';
			};
			
			function updatePackage(dg, column_uniquename) {
				var oPackage = GF_Datagrid.ReturnInstance(dg).GetRow(column_uniquename);
				location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + oPackage.package + '';
			};
			
			function uninstallPackage(dg, column_uniquename) {
				var oPackage = GF_Datagrid.ReturnInstance(dg).GetRow(column_uniquename);
				location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/delete/' + oPackage.package + '';
			};
			   
			var action_updatePackage = new GF_Action({
				caption: '{% trans %}TXT_UPDATE{% endtrans %}',
				action: updatePackage,
				img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/update.png',
				condition: function(oR) { return oR['upgrade'] == '1'; }
			});

			var action_installPackage = new GF_Action({
				caption: '{% trans %}TXT_INSTALL{% endtrans %}',
				action: installPackage,
				img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/add.png',
				condition: function(oR) { return oR['install'] == '1'; }
			});

			var action_uninstallPackage = new GF_Action({
				caption: '{% trans %}TXT_UNINSTALL{% endtrans %}',
				action: uninstallPackage,
				img: '{{ DESIGNPATH }}_images_panel/icons/datagrid/delete.png',
				condition: function(oR) { return oR['uninstall'] == '1'; }
			});
			
			var column_package = new GF_Datagrid_Column({
				id: 'package',
				caption: '{% trans %}TXT_PACKAGE{% endtrans %}',
				filter: {
					type: GF_Datagrid.FILTER_INPUT,
				},
				appearance: {
					width: GF_Datagrid.WIDTH_AUTO,
				}
			});
			
			var column_name = new GF_Datagrid_Column({
				id: 'name',
				caption: '{% trans %}TXT_NAME{% endtrans %}',
				filter: {
					type: GF_Datagrid.FILTER_INPUT,
				},
				appearance: {
					width: GF_Datagrid.WIDTH_AUTO,
				}
			});
			
			var column_server_version = new GF_Datagrid_Column({
				id: 'server_version',
				caption: '{% trans %}TXT_SERVER_VERSION{% endtrans %}',
				appearance: {
					width: GF_Datagrid.WIDTH_AUTO,
				}
			});
			
			var column_local_version = new GF_Datagrid_Column({
				id: 'local_version',
				caption: '{% trans %}TXT_LOCAL_VERSION{% endtrans %}',
				appearance: {
					width: GF_Datagrid.WIDTH_AUTO,
				}
			});

			dataProvider = new GF_Datagrid_Data_Provider({
				key: 'package',
			}, {{ packages }});
			
			var options = {
				id: 'gekolab',
				mechanics: {
					key: 'package',
					rows_per_page: {{ globalsettings.interface.datagrid_rows_per_page }},
					right_click_menu: false
				},
				event_handlers: {
					load: function(a, b) {dataProvider.Load(a, b);}
				},
				columns: [
					column_package,
					column_name,
					column_server_version,
					column_local_version,
				],
				row_actions: [
					action_updatePackage,
					action_installPackage,
					action_uninstallPackage
				],
				context_actions: [
					action_updatePackage,
					action_installPackage,
					action_uninstallPackage
				]
			};
			
			var theDatagrid = new GF_Datagrid($('#list-update'), options);
			
		});

		/*]]>*/

	

</script>
{% endif %}