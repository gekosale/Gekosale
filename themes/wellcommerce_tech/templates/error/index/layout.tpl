{% include 'header.tpl' %}
<section id="content" class="content layout-boxes">
	<div id="searchNoResults">
		<span class="ico"><img src="{{ ASSETSPATH }}img/search.ico.png" alt=""></span>
		<h1>
			Niepoprawny adres</strong>
		</h1>
		<h2>
			Taka strona nie istnieje
		</h2>
	</div>
	<div class="row">
		<div class="span12">
			<article class="article marginbt50">

				<h1 class="noborder">Nie wiesz co zrobić?</h1>
				<p>
					Przejdź na <a href="{{ path('frontend.home') }}">stronę główną serwisu.</a><br>W przypadku problemów możesz również zadzwonić lub napisać.
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
