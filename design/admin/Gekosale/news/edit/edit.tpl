{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/news-edit.png" alt=""/>{% trans %}TXT_EDIT_NEWS{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}news" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_NEWS{% endtrans %}" alt="{% trans %}TXT_NEWS{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#news" rel="reset" class="button"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/delete.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#news" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
<script type="text/javascript">
$(document).ready(function() {
	$.each(GCore.aoLanguages,function(l,language){
	    var topic = "#required_data__language_data__"+language.id+"__topic";
	    var seo = "#required_data__language_data__"+language.id+"__seo";
	    if($(seo).val() == ''){
	    	$(topic).bind('change',function(){
				xajax_doAJAXCreateSeo({
					name: $(topic).val()
				}, GCallback(function(eEvent) {
					$(seo).val(eEvent.seo).change();
				}));
	    	});
		}
		var sRefreshLink =  $('<img title="{% trans %}TXT_REFRESH_SEO{% endtrans %}" src="' + GCore.DESIGN_PATH + '_images_panel/icons/datagrid/refresh.png" />').css({
			cursor: 'pointer',
			'margin-top': '3px',
			'margin-left': '3px',
		});

		$(seo).parent().parent().append(sRefreshLink);

		sRefreshLink.click(function(){
			xajax_doAJAXCreateSeo({
				name: $(topic).val()
			}, GCallback(function(eEvent) {
				$(seo).val(eEvent.seo).change();
			}));
		});
	});
});
</script>
{% endblock %}