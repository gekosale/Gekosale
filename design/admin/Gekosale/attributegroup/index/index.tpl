{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/atributes-list.png" alt=""/>{% trans %}TXT_PRODUCT_VARIANTS{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_ATTRIBUTE_DATA_SET{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_ATTRIBUTE_DATA_SET{% endtrans %}</span></a></li>
</ul>
<script type="text/javascript">
GCore.OnLoad(function() {
	$('a[href="{{ URL }}{{ CURRENT_CONTROLLER }}/add"]').click(function() {
		GPrompt('{% trans %}TXT_ENTER_NEW_ATTRIBUTE_GROUP_NAME{% endtrans %}', function(sName) {
			GCore.StartWaiting();
			xajax_AddGroup({
				name: sName
			}, GCallback(function(eEvent) {
				if(eEvent.error != undefined){
					GMessage(eEvent.error);
				}else{
					if (eEvent.id == undefined) {
						window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
					}
					else {
						window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}/edit/' + eEvent.id;
					}
				}
			}));
		});
		return false;
	});
});
</script>

<div class="block">
	{% if existingGroups|length > 0 %}
	<div class="scrollable-tabs">
		<ul>
		{% for group in existingGroups %}
			<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/edit/{{ group.id }}">{{ group.name }}</a></li>
		{% endfor %}
		</ul>
	</div>
	{% endif %}
	<p>{% trans %}TXT_CHOOSE_ATTRIBUTE_GROUP_TO_EDIT{% endtrans %}</p>
</div>
{% endblock %}