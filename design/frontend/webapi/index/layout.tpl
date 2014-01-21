{% include 'webapi/header.tpl' %}
<ul>
{% for method in methods %}
	<li><a href="#{{ method }}">{{ method }}</a>
{% endfor %}
</ul>
{% for method in methods %}
	<div class="well well-small well-clean">
	{% include 'webapi/blocks/' ~ method ~ '.tpl' %}
	</div>
{% endfor %}
{% include 'webapi/footer.tpl' %}