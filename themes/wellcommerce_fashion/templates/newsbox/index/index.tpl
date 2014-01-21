{% extends "layoutbox.tpl" %} 
{% block content %} 
<article class="article">
	<h1>{{ box.heading }}</h1>
	{% for entry in news %}
	<div class="row-fluid">
		<div class="span12 well">
			<h3>{{ entry.topic }}</h3>
			<p><span class="badge">{% trans %}TXT_ADDDATE{% endtrans %}: {{ entry.adddate }}</span></p>
			{{ entry.summary }}
			<p>
				<a class="btn" href="{{ entry.link }}" title="{{ entry.topic }}">{% trans %}TXT_READ_MORE{% endtrans %} »</a>
			</p>
		</div>
	</div>
	{% endfor %}
</article>
{% endblock %}