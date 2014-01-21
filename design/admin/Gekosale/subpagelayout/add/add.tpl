{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/subpagelayout-add.png" alt=""/>{% trans %}TXT_SUBPAGE_LAYOUT_ADD{% endtrans %}</h2>
<ul class="possibilities">
{% if list is defined and list == 1 %}
	<li><a href="{{ URL }}subpagelayout" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SUBPAGE_LAYOUT_LIST{% endtrans %}" alt="{% trans %}TXT_SUBPAGE_LAYOUT_LIST{% endtrans %}"/></span></a></li>
</ul>
{fe_form form=$emptyList render_mode="JS"}
{% else %}
	<li><a href="{{ URL }}subpagelayout" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SUBPAGE_LAYOUT_LIST{% endtrans %}" alt="{% trans %}TXT_SUBPAGE_LAYOUT_LIST{% endtrans %}"/></span></a></li>
		<!-- <li><a href="#subpagelayout" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
		<li><a href="#subpagelayout" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
	</ul>

	{{ form }}

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

				GCore.OnLoad(function() {
					$('#subpagelayout').submit(checkForDuplicates);
				});

			/*]]>*/

	</script>

{% endif %}
{% endblock %}