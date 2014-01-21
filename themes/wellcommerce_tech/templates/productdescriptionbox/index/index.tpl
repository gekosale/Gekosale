{% extends "layoutbox.tpl" %}
{% block content %}
<article id="productInfo" class="article marginbt20">
	<div class="row-fluid row-form">
		{% if tabbed == 1 %}
		<div class="relative">
			
			{% include 'productbox/index/info_tabs.tpl' %}
		</div>
		{% else %}
		<div>
			
			<div class="clearfix"></div>
			{% include 'productbox/index/info_notabs.tpl' %}
		</div>
		{% endif %}	
	</div>
</article>
<script type="text/javascript">
{% if tabbed == 1 %}
$('#intro-links a').each(function(){
	$(this).click(function(){
		$('a[href='+ $(this).attr('href') +']').tab('show');
		return false;
	});
});

{% endif %}
$('#add-review').unbind('click').bind('click',function(){
	var params = {};
	var form = $('form#review').serializeArray();
	$.each(form, function(index,value) {
		params[value.name] = value.value;
	});
	return xajax_addOpinion({{ product.idproduct }}, params);
});
</script>
{% endblock %}