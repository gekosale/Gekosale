{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/promotion-add.png" alt=""/>{% trans %}TXT_PRODUCT_PROMOTION_LIST{% endtrans %}</h2>
<ul class="possibilities">
   <li><a href="{{ URL }}productpromotion" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PRODUCT_PROMOTION_LIST{% endtrans %}" alt="{% trans %}TXT_PRODUCT_PROMOTION_LIST{% endtrans %}"/></span></a></li>
   <!-- <li><a href="#promotion" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
   <li><a href="#promotion" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endblock %}