{% extends "layout.tpl" %}
{% block content %}

<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/orders-edit.png" alt=""/>{% trans %}TXT_ADDITIONAL_INFORMATION{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}exchangexml/index" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SPY_LIST{% endtrans %}" alt="{% trans %}TXT_SPY_LIST{% endtrans %}"/></span></a></li>
</ul>

<script>
	$(document).ready(function(){
		$('input, textarea').each(function(){
			if($(this).attr('name').substr(0, 13) == 'exchange_data') {
				$(this).attr('readonly', 'readonly');
			}
		});

		$('fieldset.GFormNode ul.navigation li.next').hide();
	});
</script>

{{ form }}

{% endblock %}