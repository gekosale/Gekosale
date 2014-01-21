{% extends "layout.tpl" %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ckeditor/ckeditor.js?v={{ appVersion }}"></script>
{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/product-duplicate.png" alt=""/>{% trans %}TXT_DUPLICATE_PRODUCT{% endtrans %}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}product" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_PRODUCTS_LIST{% endtrans %}" alt="{% trans %}TXT_PRODUCTS_LIST{% endtrans %}"/></span></a></li>
	<!-- <li><a href="#product" rel="reset" class="button" title="{% trans %}TXT_START_AGAIN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/clean.png" alt=""/>{% trans %}TXT_START_AGAIN{% endtrans %}</span></a></li> -->
	<li><a href="#product" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{{ form }}
<script type="text/javascript">

$(document).ready(function() {
	$.each(GCore.aoLanguages,function(l,language){
		var name = "#basic_pane__language_data__"+language.id+"__name";
		var seo = "#basic_pane__language_data__"+language.id+"__seo";
		if($(seo).val() == ''){
			xajax_doAJAXCreateSeo({
				name: $(name).val()
			}, GCallback(function(eEvent) {
				$(seo).val(eEvent.seo);
			}));
		}
		var sRefreshLink =  $('<img title="{% trans %}TXT_REFRESH_SEO{% endtrans %}" src="' + GCore.DESIGN_PATH + '_images_panel/icons/datagrid/refresh.png" />').css({
			cursor: 'pointer',
			'margin-top': '3px',
			'margin-left': '3px',
		});
		$(seo).parent().parent().append(sRefreshLink);

		sRefreshLink.click(function(){
			xajax_doAJAXCreateSeo({
				name: $(name).val()
			}, GCallback(function(eEvent) {
				$(seo).val(eEvent.seo);
			}));
		});
  	});
});

</script>
{% endblock %}