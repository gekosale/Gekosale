{% extends "layoutbox.tpl" %} 
{% block content %} 
<article class="article">
	<h1>{{ box.heading }}</h1>
	
	<div class="row-fluid">
		<div class="span12 ">
			<p><span class="badge">{% trans %}TXT_ADDDATE{% endtrans %}: {{ news.adddate }}</span></p>
			{{ news.content }}
			<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
				{% for otherphoto in news.otherphoto %}
				<a rel="gallery" href="{{ otherphoto.orginal }}" title="{{ news.topic }}"><img src="{{ otherphoto.small }}" alt="{{ news.topic }}"/></a>
				{% endfor %}
			</div>
			<p>
				<a class="btn" href="{{ path('frontend.news') }}">{% trans %}TXT_BACK{% endtrans %}</a>
			</p>
		</div>
	</div>
</article>
{% endblock %}