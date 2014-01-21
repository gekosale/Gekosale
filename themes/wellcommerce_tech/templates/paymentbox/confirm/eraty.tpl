{% if idorder > 0 %}
<div><p>Wniosek został przyjęty. Proszę czekać na telefon konsulatanta systemu ratalnego Żagiel.<br> 
		Twój numer zamówienia: <strong><font color="red"> {{ idorder }} </font></strong></p>
</div>	
{elseif isset($error) && $error ==1}
	<div><p>Wpisano nieprawidłowy adres URL.</p></div>
{% else %}
	<div><p>Niepoprawnie wpisany adres URL lub wniosek został już potwierdzony.</p></div>
{% endif %}
<div class="buttons">
	<a href="{{ path('frontend.home') }}"><img src="{{ DESIGNPATH }}/_images_frontend/buttons/wroc-do-zakupow.png" alt="{% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}"/></a>
</div>		