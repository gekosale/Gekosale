{% extends "layoutbox.tpl" %}
{% block content %}
<div class="row-fluid row-form">
	<h1 class="large">Krok 3. Potwierdzenie i płatność</h1>
	<div class="span11">
    	<div class="alert alert-block alert-success">
        	<h3>Twoje zamówienie zostało przyjęte.</h3>
		</div>
		<p class="marginbt20">Dziękujemy za złożenie zamówienia.</p>
                    
		<h4 class="font15">Dokonaj płatności</h4>
        <p class="marginbt20">Twoje zamówienie zostanie zrealizowane po zaakceptowaniu płatności. Dane do płatności przelewem na konto bankowe sklepu znajdują się w wysłanej wiadomości oraz poniżej:</p>
        <p class="marginbt20">
        <strong>Nazwa banku:</strong> {{ content.bankname }}<br />
        <strong>Numer rachunku:</strong> {{ content.bankacct }}<br />
        <strong>{% trans %}TXT_TITLE{% endtrans %}:</strong> {% trans %}TXT_ORDER{% endtrans %} {{ orderId }}
        </p>
                    
		<h4 class="font15">Sprawdź status zamówienia</h4>
        <p class="marginbt20">W zakładce <a href="{{ path('frontend.clientorder') }}" title="">historia zamówień</a> w Twoim koncie znajdziesz informacje na temat zamówień oraz statusu jego realizacji.</p>
                    
        <h4 class="font15">Biuro obsługi klienta</h4>
        <p class="marginbt20">Skontaktuj się z <a href="{{ path('frontend.contact') }}" title="">Biurem Obsługi klienta</a> aby uzyskać dodatkowe informacje lub dokonać zmian w zamówieniu.</p>
                    
        <p class="marginbt20">Dziękujemy za dokonanie zakupu i zapraszamy ponowne.</p>
                    
        <a href="{{ path('frontend.home') }}" title=""><i class="icon icon-arrow-left-blue"></i> Wróć do sklepu</a>
	</div>
</div>
{% endblock %}			