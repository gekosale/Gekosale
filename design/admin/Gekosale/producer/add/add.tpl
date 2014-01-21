{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/producer-add.png" alt=""/>{% trans %}TXT_ADD_PRODUCER{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}producer" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PRODUCERS_LIST{% endtrans %}" alt="{% trans %}TXT_PRODUCERS_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#producer" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#producer" rel="submit[next]" class="button" title="{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/add.png" alt=""/>{% trans %}TXT_SAVE_AND_ADD_ANOTHER{% endtrans %}</span></a></li>
	<li><a href="#producer" rel="submit" class="button" title="{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE_AND_FINISH{% endtrans %}</span></a></li>
</ul>
{{ form }}
<script type="text/javascript">
$(document).ready(function() {
	$.each(GCore.aoLanguages,function(l,language){
		var topic = "#required_data__language_data__"+language.id+"__name";
	    var seo = "#required_data__language_data__"+language.id+"__seo";
	    $(topic).bind('change',function(){
	    	xajax_doAJAXCreateSeo({
				name: $(this).val(),
				language: language.id
			}, GCallback(function(eEvent) {
				$(seo).val(eEvent.seo);
			}));
        });
	});
});
</script>
{% endblock %}