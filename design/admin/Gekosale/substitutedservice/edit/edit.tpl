{% extends "layout.tpl" %}
{% block content %}
<script type="text/javascript">
	
		/*<![CDATA[*/
	
		var ChangeContentTransMail = function(oData) {
			var gField = oData.gForm.GetField(oData.sFieldTarget);
			if (gField != undefined) {
				xajax_SetContentTransMail({
					id: oData.sValue
				}, GCallback(function(eEvent) {
					return true;
				}));
			}
		};
		
		/*]]>*/
	
</script>

<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/notification.png" alt=""/>{% trans %}TXT_NOTIFICATION_EDIT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}substitutedservice" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SUBSTITUTED_SERVICE_LIST{% endtrans %}" alt="{% trans %}TXT_SUBSTITUTED_SERVICE_LIST{% endtrans %}"/></span></a></li>
	<li><a href="#substitutedservice" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}