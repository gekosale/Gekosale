{% include 'header_checkout.tpl' %} {% block header %}
<header class="header">
	<h1>
		<a href="#" title="">WellCommerce</a>
	</h1>
	<div class="font order-step current-step3">
		<div class="step step1">
			<strong>Krok 1</strong> <span class="desc">Dane adresowe</span>
		</div>
		<div class="step step2">
			<strong>Krok 2</strong> <span class="desc">Podsumowanie</span>
		</div>
		<div class="step step3 current">
			<strong>Krok 3</strong> <span class="desc">Potwierdzenie i płatność</span>
		</div>
	</div>
</header>
{% endblock %}
<div class="order-wrap">
	<section id="content" class="content layout-boxes">
	{{ pagescheme.content }}
	</section>
</div>
{{ pagescheme.js }} 
{% include 'footer.tpl' %}
