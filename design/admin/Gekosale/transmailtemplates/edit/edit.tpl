{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.css">
{% endblock %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/xml/xml.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/css/css.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/util/loadmode.js"></script>
{% endblock %}
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

<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/newsletter-edit.png" alt=""/>{% trans %}TXT_EDIT_TEMPLATE{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}transmailtemplates" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_TRANSMAILS_LIST{% endtrans %}" alt="{% trans %}TXT_TRANSMAILS_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#transmailtemplates" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#transmailtemplates" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}