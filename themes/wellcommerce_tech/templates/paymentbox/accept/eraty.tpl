{% extends "layoutbox.tpl" %}
{% block content %}
		{% if insufficientPrice is defined and insufficientPrice == 1 %}
			<div >
				<p>Nie możesz skorzystać z systemu ratalnego Żagiel, ponieważ wartość Twojego zamówienia nie przekracza 100 zł.<br> 
					Skontaktuj się z administratorem sklepu w celu wybrania innej metody płatności. <br/>
					Twój numer zamówienia: <strong><font color="red"> {{ idorder }} </font></strong>
				</p>
			</div>
		{elseif (isset($emptyOrder) && $emptyOrder == 1)}
			<div >
				<p>Nie możesz skorzystać z systemu ratalnego Żagiel- brak złożonego zamówienia.</p>
			</div>
		{% else %}
			{% if orderData is not empty %}
			<form name='formularz_eRaty' action="https://www.eraty.pl/symulator/krok1.php" method="POST" class="block dark">
					<div >
						<p><strong>Wybrałeś płaność ratalną Żagiel.<br/> 
							Zapłać z Żaglem za zakupy</strong> lub skontaktuj się z administratorem w celu zmiany
							sposobu płatności za zamówienie.</p>	
					</div>
		
					<div class="buttons">
						<span class="button"><span><input type="submit" value="Zapłać"></span></span>
					</div>	
	
				{php} $counter = 0; {/php}
			
				{% for orderData.cart %}product in 	
					{if ($orderData.cart[$key].standard == 1)}
						{php} $counter = $counter + 1;
							$this->assign('counter', $counter);
						{/php}
						<input name="idTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ orderData.cart[$key].idproduct }}" />
						<input name="nazwaTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ orderData.cart[$key].name }}" />
						<input name="wartoscTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ orderData.cart[$key].newprice }}" />
						<input name="liczbaSztukTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ orderData.cart[$key].qty }}" />
						<input name="jednostkaTowaru{{ counter }}" readonly="readonly" type="hidden" value="sztuki" />
					{% endif %}
					{if ($orderData.cart[$key].attributes <> NULL)}
						{% for orderData.cart[$key].attributes %}attribprod in 
							{php} $counter = $counter + 1;
								$this->assign('counter', $counter);
							{/php}
							<input name="idTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ attribprod.idproduct }}" />
							<input name="nazwaTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ attribprod.name }}" />
							<input name="wartoscTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ attribprod.newprice }}" />
							<input name="liczbaSztukTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ attribprod.qty }}" />
							<input name="jednostkaTowaru{{ counter }}" readonly="readonly" type="hidden" value="sztuki" />
						{% endfor %}
					{% endif %}
				{% endfor %}
			{% endif %}

			{% if orderData.dispatchmethod.dispatchmethodcost is defined and orderData.dispatchmethod.dispatchmethodcost > 0 %}
				{php} $counter = $counter + 1;
					$this->assign('counter', $counter);
				{/php}
				<input name="idTowaru{{ counter }}" readonly="readonly" type="hidden" value="KosztPrzesylki"/>
				<input name="nazwaTowaru{{ counter }}" readonly="readonly" type="hidden" value="Koszt Przesyłki"/>
				<input name="wartoscTowaru{{ counter }}" readonly="readonly" type="hidden" value="{{ orderData.dispatchmethod.dispatchmethodcost }}" />
				<input name="liczbaSztukTowaru{{ counter }}" readonly="readonly" type="hidden" value="1" />
				<input name="jednostkaTowaru{{ counter }}" readonly="readonly" type="hidden" value="sztuki" />
			{% endif %}
			
			{% if orderData.globalPrice is defined and orderData.globalPrice > 0 %}
				<input type="hidden" name="wartoscTowarow" value="{{ orderData.globalPrice }}" />
				<input type="hidden" name="liczbaSztukTowarow" value="{{ orderData.count }}" />
				<input type="hidden" name="numerSklepu" value="{{ content.eraty.numersklepu }}" />
				<input type="hidden" name="wariantSklepu" value="{{ content.eraty.wariantsklepu }}" />
				<input type="hidden" name="sposobDostarczeniaTowaru" value="{{ orderData.dispatchmethod.dispatchmethodname }}" />
				<input type="hidden" name="nrZamowieniaSklep" value="{{ orderId }}" />

				<input type="hidden" name="pesel" value="" />
				<input type="hidden" name="imie" value="{{ orderData.clientdata.firstname }}" />
				<input type="hidden" name="nazwisko" value="{{ orderData.clientdata.surname }}" />
				<input type="hidden" name="email" value="{{ orderData.clientdata.email }}" />
				<input type="hidden" name="telKontakt" value="{{ orderData.clientdata.phone }}" />
				<input type="hidden" name="ulica" value="{{ orderData.clientdata.street }}" />
				<input type="hidden" name="nrDomu" value="{{ orderData.clientdata.streetno }}" />
				<input type="hidden" name="nrMieszkania" value="{{ orderData.clientdata.placeno }}" />
				<input type="hidden" name="miasto" value="{{ orderData.clientdata.placename }}" />
				<input type="hidden" name="kodPocz" value="{{ orderData.clientdata.postcode }}" />

				<input type="hidden" name="char" value="{{ content.eraty.char }}" />
				<input type="hidden" name="wniosekZapisany" value="{{ path('frontend.payment') }}/confirm/&id_zamowienie=" />
				<input type="hidden" name="wniosekAnulowany" value="{{ path('frontend.payment') }}/cancel/&id_zamowienie=" />
			{% endif %}
			</form>
		{% endif %}
	{% endblock %}