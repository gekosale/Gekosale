{% extends "layout.tpl" %}
{% block content %}
<script src="https://secure.przelewy24.pl/external/formy.php?id={{ paymentSettings.result.idsprzedawcy }}&sort=2&wersja=2" type="text/javascript"></script> 
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/invoice-list.png" alt=""/>Przedłuż abonament {{ client.productname }}</h2>
{{ form }}
<script type="text/javascript">
$(document).ready(function(){
	$('.navigation .with-image').val('Zapłać');
});
</script>
{% endblock %}
