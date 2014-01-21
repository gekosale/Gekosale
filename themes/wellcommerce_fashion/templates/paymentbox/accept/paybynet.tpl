{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Trwa przekierowanie na strony systemu płatności PayByNet.pl. Proszę czekać...</h3>
		</div>
	</div>
</div>
<form action="{{ content.url }}" method="post" id="paybynet">
	<input type="hidden" name="hashtrans" value="{{ content.hashtrans }}">
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#paybynet').submit();
});
</script>
{% endblock %}