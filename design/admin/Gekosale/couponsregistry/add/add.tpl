<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/rulescart-list.png" alt=""/>{% trans %}TXT_EDIT_VIEW_POINTSREWARDS{% endtrans %} "{$name}"</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}pointsrewards" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_SHOP_VIEW_LIST{% endtrans %}" alt="{% trans %}TXT_SHOP_VIEW_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#edit_pointsrewards" rel="reset" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#edit_pointsrewards" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{fe_form form=$form render_mode="JS"}
