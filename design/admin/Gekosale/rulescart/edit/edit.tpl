{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/promotion-edit.png" alt=""/>{% trans %}TXT_EDIT_RULE_CART{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}rulescart" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_RULES_CART_LIST{% endtrans %}" alt="{% trans %}TXT_RULES_CART_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#rulescart" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#rulescart" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>

<script type="text/javascript">
function openRulesCartEditor(sId) {
	if (sId == undefined) {
		window.location = '{{ URL }}{{ CURRENT_CONTROLLER }}';
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
<script>
$(document).ready(function(){

	var dateFrom;
	var dateTo;
	
	$('#required_data__datefrom, #required_data__dateto').change(function(){
		dateFrom = $('#required_data__datefrom').val();
		dateTo = $('#required_data__dateto').val();
		if(dateFrom != '' && dateTo !=''){
			if( (new Date(dateFrom).getTime() > new Date(dateTo).getTime()))
			{
				GError('Błędna data obowiązywania','Data zakończenia nie może być wcześniejsza niż rozpoczęcia.');
				$('#required_data__dateto').val(dateFrom);
			}
		}
	});
});
</script>
{% endblock %}