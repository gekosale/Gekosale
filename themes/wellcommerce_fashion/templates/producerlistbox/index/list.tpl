{% extends "layoutbox.tpl" %} 
{% block content %}
		<article class="article category-list">
			<h1>{{ box.heading }}</h1>
			<table>
			{% for producer in producers %}
				{% if loop.first %}
				<tr>
				{% endif %}
					<td class="valigntop padding10 aligncenter">
						<div class="span3 item">
							{% if producer.photo != ''%}
								<a href="{{ producer.link }}" title="{{ producer.name }}" class="aligncenter">
									<img src="{{ producer.photo }}" alt="{{ producer.name }}" />
								</a>
							{% else %}
								<a href="{{ producer.link }}" title="{{ producer.name }}" class="aligncenter">
									<h2 class="noborder">{{ producer.name }}</h2>
								</a>
							{% endif %}
						</div>
					</td>
				{% if loop.index is divisibleby(3) %}
				</tr>
    			<tr>
				{% endif %}	
			{% endfor %}
			</table>
		</article>
{% endblock %}
