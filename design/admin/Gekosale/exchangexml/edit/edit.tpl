{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/product-edit.png" alt=""/>{% trans %}TXT_EDIT_EXCHANGE{% endtrans %}</h2>
<ul class="possibilities">
   <li><a href="{{ URL }}exchangexml" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PRODUCTS_LIST{% endtrans %}" alt="{% trans %}TXT_PRODUCTS_LIST{% endtrans %}"/></span></a></li>
   <li><a href="#exchange" rel="submit" class="button ok" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>

{{ form }}

<script type="text/javascript">
	$(document).ready(function() {
		$('ul.links a').click(function(e){
			xajax_doAJAXgetProfile({
				url: $(this).attr('href')
			}, GCallback(function(eEvent){
				$('#profile_pane__profile_pattern').val(eEvent.source);
			}));

			e.preventDefault();
		});
	});

</script>

{% endblock %}