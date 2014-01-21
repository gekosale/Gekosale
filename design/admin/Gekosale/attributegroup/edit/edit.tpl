{% extends "layout.tpl" %}
{% block content %}

{% if errormessage is defined %}
<script type="text/javascript">
	$(document).ready(function(){
		GError('{{ errormessage }}');
	});
</script>
{% endif %}

<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/atributes-edit.png" alt=""/>{% trans %}TXT_PRODUCT_VARIANTS{% endtrans %}: {{ currentGroup.name|e }}</h2>
<ul class="possibilities">
 	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add" class="button" title="{% trans %}TXT_ADD_ATTRIBUTE_DATA_SET{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_ADD_ATTRIBUTE_DATA_SET{% endtrans %}</span></a></li>
	<li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}" rel="delete" class="button" title="{% trans %}TXT_DELETE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/delete.png" alt=""/>{% trans %}TXT_DELETE{% endtrans %}</span></a></li>
	<!-- <li><a href="#attributegroup" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#attributegroup" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
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

	$('a[href="{{ URL }}{{ CURRENT_CONTROLLER }}"][rel="delete"]').click(function() {
		GWarning('{% trans %}TXT_DO_YOU_REALLY_WANT_TO_DELETE_ATTRIBUTE_GROUP{% endtrans %}', '{% trans %}TXT_DO_YOU_REALLY_WANT_TO_DELETE_ATTRIBUTE_GROUP_DESCRIPTION{% endtrans %}', {
			bAutoExpand: true,
			aoPossibilities: [
				{mLink: function() {
					GCore.StartWaiting();
					xajax_DeleteGroup({
						id: '{{ currentGroup.id }}'
					}, GCallback(function(eEvent) {
						window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
					}));
				}, sCaption: GForm.Language.tree_ok},
				{mLink: GAlert.DestroyThis, sCaption: GForm.Language.tree_cancel}
			]
		});
		return false;
	});
});
</script>

<div class="block">
	<div class="scrollable-tabs">
		<ul>
		{% for group in existingGroups %}
			<li{% if currentGroup.id == group.id %} class="active"{% endif %}><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/edit/{{ group.id }}">{{ group.name|e }}</a></li>
		{% endfor %}
		</ul>
	</div>
	{{ form }}
</div>
{% endblock %}
{% block sticky %}
{% include sticky %}
{% endblock %}