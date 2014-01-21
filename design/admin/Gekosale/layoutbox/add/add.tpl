{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.css">
{% endblock %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.gradient.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/colorpicker.js?v={{ appVersion }}"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/xml/xml.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/mode/css/css.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/codemirror/lib/util/loadmode.js"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/layoutbox-add.png" alt=""/>{% trans %}TXT_LAYOUT_BOX_ADD{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}layoutbox" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_LAYOUT_BOX_LIST{% endtrans %}" alt="{% trans %}TXT_LAYOUT_BOX_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#layoutbox" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#layoutbox" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>

<script type="text/javascript">

		/*<![CDATA[*/
			var ChangeScheme = GEventHandler(function(eEvent) {
				var sSchemeId = eEvent.sValue;
				var agFields = eEvent.gForm.GetField('look').m_agFields;
				var gSelect = eEvent.gForm.GetField('choose_template');
				if (sSchemeId == '') {
					return;
				}
				xajax_GetSchemeValues({
					id: sSchemeId,
					data: eEvent.mArgument
				}, GCallback(GEventHandler(function(eEvent) {
					if (eEvent.values != undefined) {
						for (var i in agFields) {
							var gField = agFields[i];
							if ((gField.m_oOptions.sSelector == undefined) || (eEvent.values[gField.m_oOptions.sSelector] == undefined)) {
								continue;
							}
							var sSelector = gField.m_oOptions.sSelector;
							var mValue = eEvent.values[gField.m_oOptions.sSelector];
							if ((gField instanceof GFormTextField) || (gField instanceof GFormSelect)) {
								if ((gField.m_oOptions.sCssAttribute == undefined) || (mValue[gField.m_oOptions.sCssAttribute] == undefined)) {
									continue;
								}
								gField.SetValue(mValue[gField.m_oOptions.sCssAttribute]['value']);
							}
							else if (gField instanceof GFormFontStyle) {
								if (mValue['font'] == undefined) {
									continue;
								}
								gField.SetValue(mValue['font']);
							}
							else if (gField instanceof GFormColourSchemePicker) {
								if (mValue['background'] == undefined) {
									continue;
								}
								gField.SetValue(mValue['background']);
							}
							else if (gField instanceof GFormBorder) {
								if (mValue['border'] == undefined) {
									continue;
								}
								gField.SetValue(mValue['border']);
							}
							else if (gField instanceof GFormLocalFile) {
								if (mValue['icon'] == undefined) {
									continue;
								}
								gField.SetValue(mValue['icon']);
							}
						}
					}
					gSelect.SetValue('');
				})));
			});
		/*]]>*/

</script>

{{ form }}
{% endblock %}