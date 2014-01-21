{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/client-add.png" alt=""/>{% trans %}TXT_ADD_CLIENT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}client" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_CLIENTS_LIST{% endtrans %}" alt="{% trans %}TXT_CLIENTS_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#client" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#client" rel="submit[next]" class="button" title="{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}</span></a></li>
	<li><a href="#client" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
<script type="text/javascript">
$(document).ready(function(){
	$('#copy').unbind('click').bind('click', function(){
		$('#shipping_data').find('input, select').each(function(){
			var shipping = $(this).attr('id');
			var billing = shipping.replace("shipping_data__", "billing_data__");
			if(shipping != undefined && shipping != ''){
				$('#' + shipping).val($('#' + billing).val());
				if($('#' + shipping).is('select')){
					$('#' + shipping).change();
				}
			}
		});
		return false;
	});
});
</script>
{{ form }}
{% endblock %}