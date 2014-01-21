{% extends "layout.tpl" %}
{% block content %}
<script type="text/javascript">

		/*<![CDATA[*/

			var ChangeTagsForThisAction = function(oData) {
				var gField = oData.gForm.GetField(oData.sFieldTarget);
				if (gField != undefined) {
					xajax_GetAllTagsForThisAction({
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
					}));
				}
			};

		/*]]>*/

</script>

<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-add.png" alt=""/>{% trans %}TXT_ADD_TEMPLATE{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}transmailtemplates" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_TRANSMAILS_LIST{% endtrans %}" alt="{% trans %}TXT_TRANSMAILS_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#transmailtemplates" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#transmailtemplates" rel="submit[next]" class="button" title="{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}</span></a></li>
	<li><a href="#transmailtemplates" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}