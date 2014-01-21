{% include 'header.tpl' %}
<section id="content" class="content layout-boxes">
{% autoescape true %}
	<div id="searchNoResults">
		<span class="ico"><img src="{{ ASSETSPATH }}img/search.ico.png" alt=""></span>
		<h1>
			Wynik wyszukiwania dla <strong>"{{ phrase }}"</strong>
		</h1>
		<h2>
			Dla "{{ phrase }}" znaleziono <strong>0</strong> pozycji.
		</h2>
	</div>
{% endautoescape %}
	<div class="row">
		<div class="span12">
			<article class="article marginbt50">

				<h1 class="noborder">Nie znalazłeś produktu, którego szukasz?</h1>
				<p>
					Sformułuj inaczej swoje zapytanie i raz jeszcze skorzystaj z
					wyszukiwarki lub zapoznaj się z propozycjami poniżej.<br>W sprawie
					produktu możesz również zadzwonić lub napisać.
				</p>

				{% if defaultcontact is not empty %}
				<div class="row-fluid">
					<div class="span3 nomargin">
						<a href="{{ path('frontend.contact') }}" title="{{ defaultcontact.name }}" class="email">{{ defaultcontact.email }}</a>
					</div>
					<div class="pull-left phone nomargin">
						<h3 class="font">{{ defaultcontact.phone }}</h3>
						{% if defaultcontact.businesshours != '' %}<span>{{ defaultcontact.businesshours }}</span>{% endif %}
					</div>
				</div>
				{% endif %}

			</article>

			{% include 'products.tpl' %}
		</div>
	</div>
</section>
{% include 'footer.tpl' %}
