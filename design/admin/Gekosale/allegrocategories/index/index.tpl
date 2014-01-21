{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/allegro.js"></script>
{% endblock %}
{% block content %}
{% if errormsg %}
	{% include "allegro/error.tpl" %}
{% else %}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/allegro.js"></script>
<h2><img src="{{ DESIGNPATH }}_images_panel/logos/logo_allegro.jpg" alt=""/>{% trans %}TXT_ALLEGRO_CATEGORIES{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}allegrocategories/confirm" id="refresh" class="button"><span>{% trans %}TXT_REFRESH_ALLEGRO_CATEGORIES{% endtrans %}</span></a></li>
	<li><a href="#allegro_categories" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
{% endif %}
{% endblock %}