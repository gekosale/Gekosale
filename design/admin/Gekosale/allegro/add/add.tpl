{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/allegro.js"></script>
{% endblock %}
{% block content %}
{% if errormsg %}
	{% include "allegro/error.tpl" %}
{% else %}
<h2><img src="{{ DESIGNPATH }}_images_panel/logos/logo_allegro.jpg" alt=""/>{% trans %}TXT_ALLEGRO_NEW_AUCTION{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="#add_allegro" rel="reset" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li>
	<li><a href="#add_allegro" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_ALLEGRO_GO_TO_SECOND_STEP{% endtrans %}</span></a></li>
</ul>
{{ form }}
<script>
$(document).ready(function(){
	$('#optionstemplate_data__optionstemplateid').change(function(){
		var id = $(this).val();
		if(id == 0){
			location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/add';
		}else{
			location.href = '{{ URL }}{{ CURRENT_CONTROLLER }}/add/' + id + '';
		}
	});
});
</script>
{% endif %}
{% endblock %}