{% extends "layout.tpl" %}
{% block content %}
<script type="text/javascript">
	$(document).ready(function(){
		GError('{{ permerror }}');
	});
</script>

{% endblock %}