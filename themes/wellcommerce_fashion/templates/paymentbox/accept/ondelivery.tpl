{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Twoje zamówienie zostało przyjęte.</h3>
		</div>
		<p class="marginbt20">Dziękujemy za złożenie zamówienia.</p>

		<h4 class="font15">Sprawdź status zamówienia</h4>
        <p class="marginbt20">W zakładce <a href="{{ path('frontend.clientorder') }}" title="">historia zamówień</a> w Twoim koncie znajdziesz informacje na temat zamówień oraz statusu jego realizacji.</p>

        <h4 class="font15">Biuro obsługi klienta</h4>
        <p class="marginbt20">Skontaktuj się z <a href="{{ path('frontend.contact') }}" title="">Biurem Obsługi klienta</a> aby uzyskać dodatkowe informacje lub dokonać zmian w zamówieniu.</p>

        <p class="marginbt20">Dziękujemy za dokonanie zakupu i zapraszamy ponowne.</p>

        <a href="{{ path('frontend.home') }}" title=""><i class="icon icon-arrow-left-blue"></i> Wróć do sklepu</a>
	</div>
</div>
{% endblock %}