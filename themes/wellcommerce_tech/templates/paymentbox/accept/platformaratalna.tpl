{% extends "layoutbox.tpl" %}
{% block content %}
<form action="http://www.platformaratalna.pl/kalkulator" method="post" id="platformaratalna">
	<input type="hidden" name="partner" value="{{ content.idpartnera }}">
	<input type="hidden" name="kwota" value="{{ content.kwota }}">
    <input type="hidden" name="info" value="{{ content.info }}" />
    <input type="hidden" name="sklep" value="{{ path('frontend.payment', {"action": 'report', "param": 'platformaratalna'}) }}" />
</form>

<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Trwa przekierowanie na strony systemu płatności PlatformaRatalna.pl. Proszę czekać...</h3>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#platformaratalna').submit();
});
</script>
{% endblock %}	
