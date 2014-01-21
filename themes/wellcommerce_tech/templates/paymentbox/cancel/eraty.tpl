	{% if idorder > 0 %}
		<div>
			<p>Wniosek został odrzucony. Proszę skontaktować się z administratorem sklepu w celu wybrania innej metody płatności.<br> 
				Twój numer zamówienia: <strong><font color="red"> {{ idorder }} </font></strong>
			</p>
		</div>	
	{elseif isset($error) && $error==1}
		<div>
			<p>Wpisano nieprawidłowy adres URL.</p>
		</div>
	{% else %}
		<div>
			<p>Niepoprawnie wpisany adres URL lub wniosek został już anulowany.</p>
		</div>
	{% endif %}