{% extends "layoutbox.tpl" %} 
{% block content %}
		<article class="article category-list">
			<h1>{{ box.heading }}</h1>
			<table>
			{% for category in categories %}
				{% if loop.first %}
				<tr>
				{% endif %}
					<td class="valigntop padding10">
						<div class="span3 item">
							<a href="{{ category.link }}" title="{{ category.label }}"><h2>{{ category.label }}</h2></a>
							<ul class="nav nav-pills nav-stacked">
								{% for subcategory in category.children %}
								<li><a href="{{ subcategory.link }}">{{ subcategory.label }}</a></li>
								{% endfor %}   
							</ul>
						</div>
					</td>
				{% if loop.index is divisibleby(4) %}
				</tr>
    			<tr>
				{% endif %}	
			{% endfor %}
			</table>
		</article>
{% endblock %}
