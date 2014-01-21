{% extends "layout.tpl" %}
{% block content %}
<script type="text/javascript">
	
		/*<![CDATA[*/
			
			var ChangeClientsListForNotification = function(oData) {
				var gField = oData.gForm.GetField(oData.sFieldTarget);
				if (gField != undefined) {
					xajax_GetAllClientsForNotification({
						id: oData.sValue
					}, GCallback(function(eEvent) {
						var aoValues = [];
						for (var j in eEvent.data) {
							aoValues.push({
								sCaption: eEvent.data[j][0],
								sValue: eEvent.data[j][1]
							});
						}
						gField.ChangeItems(aoValues, eEvent.title);
						if(oData.sValue > 0) {
							$('#link').html("<a href=\"/admin/substitutedservicesend/add/"+oData.sValue+"\"> Wyślij powiadomienie </a>");
						}
					}));
				}
			};
		/*]]>*/
	
</script>

<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-list.png" alt=""/>Powiadomienia- raport z wysyłki</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}substitutedservicesend" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_RULES_CATALOG_LIST{% endtrans %}" alt="{% trans %}TXT_RULES_CATALOG_LIST{% endtrans %}"/></span></a></li>
</ul>

<div class="column wide-collapsed">
	{{ form }}
</div>
{% endblock %}