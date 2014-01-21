{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/rulescart-list.png" alt=""/>{% trans %}TXT_RULES_CART_LIST{% endtrans %}</h2>

<script type="text/javascript">
	
		/*<![CDATA[*/
			function openRulesCartEditor(sId) {
				if (sId == undefined) {
					window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
				}
				else {
					window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + sId;
				}
			};
		/*]]>*/
	
</script>

<div class="block">
	{% if total == 0 %}
		<p class="empty">Obecnie lista reguł promocyjnych w koszyku jest pusta. Dodaj regułę aby określić warunki i akcje jakie zajdą przy ich spełnieniu w koszyku klienta</p>
	{% endif %}
	{{ tree }}
</div>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}