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
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/layoutbox-edit.png" alt=""/>{% trans %}TXT_LAYOUT_BOX_EDIT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="#delete" class="button delete" id="delete"><span>{% trans %}TXT_LAYOUT_BOX_DELETE{% endtrans %}</span></a></li>
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt="{% trans %}TXT_LAYOUT_BOX_ADD{% endtrans %}"/>{% trans %}TXT_LAYOUT_BOX_ADD{% endtrans %}</span></a></li>
	<!-- <li><a href="#layoutbox" rel="reset" class="button reset"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#layoutbox" rel="submit" class="button ok"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
	<li><a href="#layoutbox" rel="submit[continue]" class="button continue"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_CONTINUE{% endtrans %}</span></a></li>
</ul>

<script type="text/javascript">
$(document).ready(function(){
	$('#delete').unbind('click').bind('click', function(){
		var title = '{% trans %}TXT_LAYOUT_BOX_DELETE{% endtrans %}';
		var msg = '{% trans %}TXT_DELETE_CONFIRM{% endtrans %} ?';
		var params = {};
		var func = function(p) {
			return xajax_DeleteLayoutBox({{ id }});
		};
		new GF_Alert(title, msg, func, true, params);
		return false;
	});
});

		/*<![CDATA[*/
			var iSchemeChanges = 0;
			var ChangeScheme = GEventHandler(function(eEvent) {
				if (iSchemeChanges++ < 1) {
					return;
				}
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
<script type="text/javascript">
function openLayoutBoxEditor(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/';
	}
	else {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + sId;
	}
};
</script>

<div class="layout-two-columns">
	<div class="column narrow-collapsed">
		<div class="block">
			{{ tree }}
		</div>
	</div>
	<div class="column wide-collapsed">
		{{ form }}
	</div>
</div>
<style>
.layout-two-columns > .column.narrow-collapsed {
	width: 270px;
	margin-right: -1px;
}
.layout-two-columns > .column.wide-collapsed {
	width: 912px;
}
</style>
{% endblock %}