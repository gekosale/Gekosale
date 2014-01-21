{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/subpagelayout-edit.png" alt=""/>{% trans %}TXT_SUBPAGE_LAYOUT_EDIT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}subpagelayout" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SUBPAGE_LAYOUT_LIST{% endtrans %}" alt="{% trans %}TXT_SUBPAGE_LAYOUT_LIST{% endtrans %}"/></span></a></li>
	{% if viewSpecific is defined %}
		<li><a href="#" class="button" rel="use-global"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/delete-2.png" title="{% trans %}TXT_SUBPAGE_LAYOUT_DISBAND_VIEW_SPECIFIC{% endtrans %}" alt="{% trans %}TXT_SUBPAGE_LAYOUT_DISBAND_VIEW_SPECIFIC{% endtrans %}"/>{% trans %}TXT_SUBPAGE_LAYOUT_DISBAND_VIEW_SPECIFIC{% endtrans %}</span></a></li>
	{% endif %}
	<!-- <li><a href="#subpagelayout" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#subpagelayout" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>

<script type="text/javascript">
function openSubpageEditor(sId) {
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

<script type="text/javascript">

		/*<![CDATA[*/

			var checkForDuplicates = GEventHandler(function(eEvent) {
				var jSelects = $('#columns_data > .GFormRepetition select');
				var iSelects = jSelects.length;
				for (var i = 0; i < iSelects; i++) {
					var jSelect1 = jSelects.eq(i);
					for (var j = i + 1; j < iSelects; j++) {
						var jSelect2 = jSelects.eq(j);
						if (jSelect1.val() == jSelect2.val()) {
							GCore.StopWaiting();
							GError('Wykryto duplikaty', 'Na jednej podstronie nie może wystąpić kilka takich samych boksów. Zduplikowane boksy to: "' + jSelect1.find('option:selected').text() + '"');
							return false;
						}
					}
				}
				return true;
			});

			var disbandViewSpecific = GEventHandler(function(eEvent) {
				xajax_DeleteSubpageLayout({
					idsubpagelayout: '{{ subpageLayout.id }}'
				}, GCallback(function(eEvent) {
					location.href = '{{ URL }}subpagelayout/index';
				}));
				return false;
			});

			GCore.OnLoad(function() {
				$('#subpagelayout').submit(checkForDuplicates);
				$('a[rel="use-global"]').click(disbandViewSpecific);
			});

		/*]]>*/

</script>
<style>
.layout-two-columns > .column.narrow-collapsed {
	width: 370px;
	margin-right: -1px;
}
.layout-two-columns > .column.wide-collapsed {
	width: 812px;
}
</style>
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}