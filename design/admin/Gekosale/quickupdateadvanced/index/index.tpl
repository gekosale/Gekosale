{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/news-list.png" alt=""/>{% trans %}TXT_QUICK_UPDATE_ADVANCED{% endtrans %}</h2>

{% if success %}
<script type="text/javascript">
	$(document).ready(function(){
		GMessage('{% trans %}TXT_JS_PROGRESS_INDICATOR_SUCCESS{% endtrans %}', 'Zaktualizowano <strong>{{ success }}</strong> produktów');
	});
</script>
{% endif %}

{{ form }}

{% endblock %}