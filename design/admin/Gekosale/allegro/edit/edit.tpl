<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/allegro.js"></script>
<h2><img src="{{ DESIGNPATH }}_images_panel/logos/logo_allegro.jpg" alt=""/>{% trans %}TXT_ALLEGRO{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="#edit_allegro" rel="reset" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li>
	<li><a href="#edit_allegro" rel="submit" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>

{fe_form form=$form render_mode="JS"}